<div class="">
    @if (!empty($batch))
    <div class="col-md-6" wire:poll.keep-alive>
            <label>Capture {{ $status }} : </label>
        <progress style="padding: 15px;margin-top:-10px" id="file" value="{{ $job }}" max="{{ $max }}"></progress>
    </div>
    @endif
</div>