<div style="text-align: center">

    <div class="header-action">
        <a class="download" wire:click="export">Download Semua</a>
    </div>

    <h1>{{ $bus_id }}</h1>

    @if($exporting && !$finish)
    <div wire:poll.500ms="updateProgress">
        Current time: {{ now() }}
    </div>
    @endif

    @if($finish)
    <div class="header-action">
        <a class="ready" wire:click="downloadExport">File Siap</a>
    </div>
    @endif

</div>