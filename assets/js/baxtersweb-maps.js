(function () {
    'use strict';
    if (typeof L === 'undefined') return;

    const tileLayer = { url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', options: { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' } };

    function json(value, fallback) { try { return JSON.parse(value || ''); } catch (e) { return fallback; } }
    function esc(value) { const d=document.createElement('div'); d.textContent=String(value||''); return d.innerHTML; }
    function routeLabel(index, sequence) {
        if (sequence === 'numeric') return String(index + 1);
        let label='', value=index+1; while(value>0){value--;label=String.fromCharCode(65+(value%26))+label;value=Math.floor(value/26);} return label;
    }
    function normalise(items, kind) {
        return (Array.isArray(items)?items:[]).map(function(p,i){return {
            lat:parseFloat(p.lat), lng:parseFloat(p.lng), zoom:parseInt(p.zoom||10,10), title:p.title||'', description:p.description||'', type:p.type||'', kind:kind,
            markerStyle:p.markerStyle||'builtin', builtinIcon:p.builtinIcon||'location-alt', themeIconClass:p.themeIconClass||'', color:p.color||'', index:i
        };}).filter(function(p){return Number.isFinite(p.lat)&&Number.isFinite(p.lng);});
    }
    function routeIcon(label) {
        return L.divIcon({className:'bxtr-marker bxtr-marker--route',html:'<span class="bxtr-marker__route"><span class="bxtr-marker__number">'+esc(label)+'</span></span>',iconSize:[30,40],iconAnchor:[15,40],popupAnchor:[0,-36]});
    }
    function poiIcon(point, defaultColor) {
        const color=/^#[0-9a-f]{6}$/i.test(point.color)?point.color:defaultColor;
        let inner='';
        if(point.markerStyle==='theme' && point.themeIconClass) inner='<i class="'+esc(point.themeIconClass)+'" aria-hidden="true"></i>';
        else if(point.markerStyle!=='plain') inner='<span class="dashicons dashicons-'+esc(point.builtinIcon)+'" aria-hidden="true"></span>';
        return L.divIcon({className:'bxtr-marker bxtr-marker--poi',html:'<span class="bxtr-marker__poi-icon" style="background-color:'+esc(color)+'">'+inner+'</span>',iconSize:[34,34],iconAnchor:[17,17],popupAnchor:[0,-18]});
    }
    function clusterIcon(count, color) {
        const style=color?' style="background-color:'+esc(color)+'"':'';
        return L.divIcon({className:'bxtr-marker bxtr-marker--cluster',html:'<span class="bxtr-marker__cluster"'+style+'>'+count+'</span>',iconSize:[38,38],iconAnchor:[19,19]});
    }
    function popup(point, label) {
        const markerText=(window.BXTRMapsFrontend&&BXTRMapsFrontend.marker)||'Marker';
        const poiText=(window.BXTRMapsFrontend&&BXTRMapsFrontend.pointOfInterest)||'Point of Interest';
        let heading=point.title || (point.kind==='route'?markerText+' '+label:(point.type||poiText));
        let html='<div class="bxtr-popup"><strong>'+esc(heading)+'</strong>';
        if(point.kind==='poi' && point.type && point.title) html+='<span class="bxtr-popup__type">'+esc(point.type)+'</span>';
        if(point.description) html+='<div class="bxtr-popup__description">'+point.description+'</div>';
        return html+'</div>';
    }

    function initMap(el) {
        const sequence=el.dataset.markerSequence==='numeric'?'numeric':'alphabetic';
        const stops=normalise(json(el.dataset.stops,[]),'route');
        const pois=normalise(json(el.dataset.pois,[]),'poi');
        if(!stops.length&&!pois.length) return;
        stops.forEach(function(p,i){p.label=routeLabel(i,sequence);});
        const all=stops.concat(pois), first=all[0];
        const routeColor=el.dataset.routeColor||'#3388ff', poiColor=el.dataset.poiMarkerColor||'#f59e0b';
        const map=L.map(el,{scrollWheelZoom:false}).setView([first.lat,first.lng],first.zoom||9);
        L.tileLayer(tileLayer.url,tileLayer.options).addTo(map);
        const bounds=all.map(function(p){return[p.lat,p.lng];}); if(bounds.length>1) map.fitBounds(bounds,{padding:[40,40]});

        // Cached road geometry. Straight line is used only when no cached route exists.
        if(el.dataset.drawRoute!=='no' && stops.length>1){
            const geometry=json(el.dataset.routeGeometry,{});
            if(geometry && geometry.type==='LineString' && Array.isArray(geometry.coordinates) && geometry.coordinates.length){
                L.geoJSON(geometry,{style:{color:routeColor,weight:4,opacity:.95}}).addTo(map);
            } else {
                L.polyline(stops.map(function(p){return[p.lat,p.lng];}),{color:routeColor,weight:3,opacity:.65,dashArray:'7 7'}).addTo(map);
            }
        }

        // Route markers stay ordered. Exact duplicates are fanned slightly so both remain usable.
        const duplicateCounts={}; stops.forEach(function(p){const k=p.lat.toFixed(6)+','+p.lng.toFixed(6);duplicateCounts[k]=(duplicateCounts[k]||0)+1;});
        const duplicateSeen={};
        stops.forEach(function(p,i){
            let ll=L.latLng(p.lat,p.lng), k=p.lat.toFixed(6)+','+p.lng.toFixed(6);
            if(duplicateCounts[k]>1){
                const n=duplicateSeen[k]||0; duplicateSeen[k]=n+1; const angle=(Math.PI*2*n/duplicateCounts[k])-Math.PI/2;
                const projected=map.project(ll,map.getZoom()).add(L.point(Math.cos(angle)*18,Math.sin(angle)*18)); ll=map.unproject(projected,map.getZoom());
                L.polyline([[p.lat,p.lng],ll],{color:'#777',weight:1,opacity:.7}).addTo(map);
            }
            L.marker(ll,{icon:routeIcon(p.label),zIndexOffset:1000+i}).addTo(map).bindPopup(popup(p,p.label));
        });

        const poiLayer=L.layerGroup().addTo(map), spiderLayer=L.layerGroup().addTo(map);
        function addPoi(point, latlng){ L.marker(latlng||[point.lat,point.lng],{icon:poiIcon(point,poiColor),zIndexOffset:500}).addTo(poiLayer).bindPopup(popup(point,'')); }
        function spiderfy(group, center){
            spiderLayer.clearLayers(); poiLayer.clearLayers(); const radius=Math.max(34,group.length*7);
            group.forEach(function(point,i){const angle=(Math.PI*2*i/group.length)-Math.PI/2; const px=map.latLngToLayerPoint(center).add(L.point(Math.cos(angle)*radius,Math.sin(angle)*radius)); const ll=map.layerPointToLatLng(px); L.polyline([center,ll],{color:'#777',weight:1,opacity:.65}).addTo(spiderLayer); addPoi(point,ll);});
        }
        function renderPois(){
            spiderLayer.clearLayers(); poiLayer.clearLayers(); if(!pois.length) return;
            if(el.dataset.clusterPois!=='yes'){pois.forEach(function(p){addPoi(p);});return;}
            const unused=pois.slice(), radius=44;
            while(unused.length){
                const seed=unused.shift(), seedPx=map.latLngToLayerPoint([seed.lat,seed.lng]), group=[seed];
                for(let i=unused.length-1;i>=0;i--){const px=map.latLngToLayerPoint([unused[i].lat,unused[i].lng]);if(seedPx.distanceTo(px)<=radius){group.push(unused[i]);unused.splice(i,1);}}
                if(group.length===1){addPoi(seed);continue;}
                const center=L.latLng(group.reduce((a,p)=>a+p.lat,0)/group.length,group.reduce((a,p)=>a+p.lng,0)/group.length);
                const customColors=group.map(function(p){return /^#[0-9a-f]{6}$/i.test(p.color)?p.color.toLowerCase():'';}).filter(Boolean); const clusterColor=customColors.length && customColors.every(function(c){return c===customColors[0];})?customColors[0]:poiColor;
                const marker=L.marker(center,{icon:clusterIcon(group.length,clusterColor),zIndexOffset:700}).addTo(poiLayer);
                marker.on('click',function(){ if(map.getZoom()<map.getMaxZoom()-1){map.fitBounds(group.map(function(p){return[p.lat,p.lng];}),{padding:[60,60],maxZoom:map.getZoom()+2});}else{spiderfy(group,center);} });
            }
        }
        renderPois(); map.on('zoomend moveend',renderPois);
        setTimeout(function(){map.invalidateSize();},100);
    }

    document.querySelectorAll('.bxtr-map[data-stops]').forEach(initMap);
})();
