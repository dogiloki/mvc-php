@php
    $id??="global-modal";
    $view="";
    $properties=[];
@endphp
<div class="modal-overlay" id="{{$id}}" style="display: none">
    <div class="modal-container">
        <div class="close-btn">
            <svg id="modal-close-btn" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <!-- Círculo -->
                <circle cx="12" cy="12" r="10" stroke="gray" fill="white" />
                <!-- Línea diagonal 1 -->
                <line x1="8" y1="8" x2="16" y2="16" />
                <!-- Línea diagonal 2 -->
                <line x1="16" y1="8" x2="8" y2="16" />
            </svg>
        </div>
        <div class="modal-content">
            @include($view,$properties)
        </div>
    </div>
</div>