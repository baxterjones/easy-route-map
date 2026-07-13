(function(){
'use strict';
document.addEventListener('DOMContentLoaded',function(){
    document.querySelectorAll('.bxtr-colour-control').forEach(function(control){
        const text=control.querySelector('.bxtr-colour-text'); const picker=control.querySelector('.bxtr-colour-picker');
        if(!text||!picker)return;
        picker.addEventListener('input',function(){text.value=picker.value; text.dispatchEvent(new Event('input',{bubbles:true}));});
        text.addEventListener('input',function(){if(/^#[0-9a-f]{6}$/i.test(text.value))picker.value=text.value;});
    });

    const fieldModeInputs=document.querySelectorAll('input[name="bxtr_field_group_mode"]');
    function updateFieldMode(){
        const selected=document.querySelector('input[name="bxtr_field_group_mode"]:checked');
        const mode=selected?selected.value:'new';
        document.querySelectorAll('.bxtr-field-mode').forEach(function(panel){panel.classList.add('is-hidden');});
        const active=document.querySelector('.bxtr-field-mode--'+mode);
        if(active)active.classList.remove('is-hidden');
        document.querySelectorAll('.bxtr-choice-card').forEach(function(card){card.classList.remove('is-selected');});
        if(selected){const card=selected.closest('.bxtr-choice-card');if(card)card.classList.add('is-selected');}
    }
    fieldModeInputs.forEach(function(input){input.addEventListener('change',updateFieldMode);});
    updateFieldMode();

    const iconMode=document.getElementById('bxtr_poi_icon_mode');
    function updateIconRows(){if(!iconMode)return;document.querySelectorAll('.bxtr-icon-option').forEach(function(row){row.classList.add('is-hidden');});const row=document.querySelector('.bxtr-icon-option--'+iconMode.value);if(row)row.classList.remove('is-hidden');}
    if(iconMode){iconMode.addEventListener('change',updateIconRows);updateIconRows();}

    const el=document.getElementById('bxtr-preview-map');
    if(!el||typeof L==='undefined')return;
    const map=L.map(el,{scrollWheelZoom:false}).setView([-33.925,18.424],12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; OpenStreetMap contributors'}).addTo(map);
    const route=[[-33.925,18.418],[-33.918,18.431],[-33.929,18.441]];
    function getVar(name,fallback){return getComputedStyle(el).getPropertyValue(name).trim()||fallback;}
    function escapeHtml(value){const div=document.createElement('div');div.textContent=String(value||'');return div.innerHTML;}
    function selectedValue(name,fallback){const input=document.querySelector('input[name="'+name+'"]:checked');return input?input.value:fallback;}
    function routeLabel(index){return selectedValue('bxtr_marker_sequence','alphabetic')==='numeric'?String(index+1):String.fromCharCode(65+index);}
    function routeIcon(label){return L.divIcon({className:'bxtr-marker bxtr-marker--route',html:'<span class="bxtr-marker__route"><span class="bxtr-marker__number">'+escapeHtml(label)+'</span></span>',iconSize:[30,40],iconAnchor:[15,40],popupAnchor:[0,-36]});}

    let routeLine=null;
    let routeMarkers=[];
    function renderRoutePreview(){
        if(routeLine){map.removeLayer(routeLine);routeLine=null;}
        routeMarkers.forEach(function(marker){map.removeLayer(marker);});
        routeMarkers=[];
        if(selectedValue('bxtr_draw_route','yes')==='yes'){
            routeLine=L.polyline(route,{color:getVar('--bxtr-route-color','#3388ff'),weight:4}).addTo(map);
        }
        route.forEach(function(point,index){
            const marker=L.marker(point,{icon:routeIcon(routeLabel(index))}).addTo(map);
            if(index===1){
                const title=(window.BXTRMapsAdmin&&BXTRMapsAdmin.clickedMarkerTitle)||'Example Map Marker';
                const description=(window.BXTRMapsAdmin&&BXTRMapsAdmin.clickedMarkerDescription)||'This is dummy popup content so you can preview the marker and popup styling.';
                marker.bindPopup('<div class="bxtr-popup"><strong>'+escapeHtml(title)+'</strong><div class="bxtr-popup__description"><p>'+escapeHtml(description)+'</p></div></div>');
            }
            routeMarkers.push(marker);
        });
        if(routeMarkers[1])routeMarkers[1].openPopup();
    }

    function poiIcon(icon,color,mode){let inner='';if(mode==='theme')inner='<i class="dashicons dashicons-star-filled"></i>';else if(mode!=='plain')inner='<span class="dashicons dashicons-'+icon+'"></span>';return L.divIcon({className:'bxtr-marker bxtr-marker--poi',html:'<span class="bxtr-marker__poi-icon" style="background-color:'+escapeHtml(color)+'">'+inner+'</span>',iconSize:[34,34],iconAnchor:[17,17]});}
    let poiMarkers=[];
    function renderPoiPreview(){poiMarkers.forEach(function(m){map.removeLayer(m);});poiMarkers=[];const color=getVar('--bxtr-poi-marker-color','#f59e0b');const mode=iconMode?iconMode.value:'builtin';const icon=(document.getElementById('bxtr_poi_default_icon')||{}).value||'location-alt';const poiLabel=(window.BXTRMapsAdmin&&BXTRMapsAdmin.pointOfInterest)||'Point of Interest';poiMarkers.push(L.marker([-33.921,18.426],{icon:poiIcon(icon,color,mode)}).addTo(map).bindPopup('<strong>'+escapeHtml(poiLabel)+'</strong>'));poiMarkers.push(L.marker([-33.928,18.435],{icon:poiIcon(icon,color,mode)}).addTo(map).bindPopup('<strong>'+escapeHtml((window.BXTRMapsAdmin&&BXTRMapsAdmin.exampleExtraMarker)||'Example supporting point.')+'</strong>'));}

    renderRoutePreview();
    renderPoiPreview();
    map.fitBounds(route,{paddingTopLeft:[60,90],paddingBottomRight:[60,60],maxZoom:14});
    setTimeout(function(){map.invalidateSize();map.setZoom(Math.min(map.getZoom()+2,map.getMaxZoom()));if(routeMarkers[1]){routeMarkers[1].openPopup();map.panBy([0,-20],{animate:false});}},150);

    function bindStyle(inputName,cssVar,callback){const input=document.querySelector('[name="'+inputName+'"]');if(!input)return;input.addEventListener('input',function(){if(input.value)el.style.setProperty(cssVar,input.value);if(callback)callback(input.value);});}
    bindStyle('bxtr_marker_color','--bxtr-marker-color',renderRoutePreview);
    bindStyle('bxtr_marker_number_color','--bxtr-marker-number-color',renderRoutePreview);
    bindStyle('bxtr_poi_marker_color','--bxtr-poi-marker-color',renderPoiPreview);
    bindStyle('bxtr_route_color','--bxtr-route-color',function(v){if(routeLine)routeLine.setStyle({color:v});});
    bindStyle('bxtr_border_radius','--bxtr-border-radius');
    document.querySelectorAll('input[name="bxtr_draw_route"], input[name="bxtr_marker_sequence"]').forEach(function(input){input.addEventListener('change',renderRoutePreview);});
    if(iconMode)iconMode.addEventListener('change',renderPoiPreview);
    const defaultIcon=document.getElementById('bxtr_poi_default_icon');if(defaultIcon)defaultIcon.addEventListener('change',renderPoiPreview);
});
})();
