{{-- Settings Field Partial --}}
<div class="col-md-6">
    <div class="form-group" style="margin-bottom: 25px;">
        <label class="control-label" style="color: #333; font-weight: 500;">
            {{ $setting['label'] }}
        </label>

        @if($setting['type'] === 'boolean')
            <div class="togglebutton" style="margin-top: 5px;">
                <label>
                    <input type="checkbox" name="settings[{{ $index }}][value]" value="1" {{ $setting['value'] ? 'checked' : '' }}>
                    <span class="toggle"></span>
                    {{ $setting['value'] ? 'Enabled' : 'Disabled' }}
                </label>
            </div>
            <input type="hidden" name="settings[{{ $index }}][id]" value="{{ $setting['id'] }}">
        @elseif($setting['type'] === 'integer')
            <input type="number" class="form-control" name="settings[{{ $index }}][value]"
                value="{{ $setting['raw_value'] }}" style="margin-top: 5px;">
            <input type="hidden" name="settings[{{ $index }}][id]" value="{{ $setting['id'] }}">
        @elseif(str_contains($setting['key'], 'color'))
            <input type="color" class="form-control" name="settings[{{ $index }}][value]"
                value="{{ $setting['raw_value'] }}" style="margin-top: 5px; width: 100px; height: 40px; padding: 2px;">
            <input type="hidden" name="settings[{{ $index }}][id]" value="{{ $setting['id'] }}">
        @else
            <input type="text" class="form-control" name="settings[{{ $index }}][value]" value="{{ $setting['raw_value'] }}"
                style="margin-top: 5px;">
            <input type="hidden" name="settings[{{ $index }}][id]" value="{{ $setting['id'] }}">
        @endif

        @if($setting['description'])
            <small class="text-muted" style="display: block; margin-top: 5px;">
                <i class="material-icons" style="font-size: 14px; vertical-align: middle;">help_outline</i>
                {{ $setting['description'] }}
            </small>
        @endif
    </div>
</div>