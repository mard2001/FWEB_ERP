<div id="loadingScreen" class="pb-5 w-100 h-100 d-flex justify-content-center align-items-center loadingScreen" style="background-color: rgba(0, 0, 0, 0.1);">
    <span class="loader" style="height: 42px; width: 42px"></span>
</div>

<div class="w-100 overflow-auto opacity-0" id="dattableDiv" style="font-size: 14px;">
    <table class="mdl-data-table w-100 rmvBorder {{$class ?? ""}}" id="{{ $id ?? "getXmlData" }}">
        <thead class="text-white" style="background-color: #33336F;">
            <tr>
                {{ $td }}
            </tr>
        </thead>
    </table>
</div>