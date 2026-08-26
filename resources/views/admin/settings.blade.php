@extends('admin.layouts.admin')

@section('title', trans('extended-translation::admin.settings.title'))

@section('content')
    <form action="{{ route('extended-translation.admin.settings.update') }}" method="POST">
        @csrf

        <div class="card shadow mb-4">
            <div class="card-header">
                {{ trans('extended-translation::admin.settings.languages_heading') }}
            </div>
            <div class="card-body">
                <p class="text-body-secondary">{{ trans('extended-translation::admin.settings.help') }}</p>

                <div class="mb-0">
                    <label class="form-label">{{ trans('extended-translation::admin.settings.available') }}</label>

                    @error('locales')
                    <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                    @enderror

                    <div class="row">
                        @foreach($installed as $code => $name)
                            <div class="col-md-4 col-lg-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input @error('locales') is-invalid @enderror" type="checkbox" name="locales[]" id="locale-{{ $code }}" value="{{ $code }}" @checked($enabled->contains($code))>
                                    <label class="form-check-label" for="locale-{{ $code }}">
                                        {{ $name }}
                                        <span class="text-body-secondary">({{ $code }})</span>
                                        @if($code === $defaultLocale)
                                            <span class="badge text-bg-secondary">{{ trans('extended-translation::admin.settings.default') }}</span>
                                        @endif
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header">
                {{ trans('extended-translation::admin.settings.inject_heading') }}
            </div>
            <div class="card-body">
                <p class="text-body-secondary">{{ trans('extended-translation::admin.settings.inject_help') }}</p>

                <div class="mb-0 form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="injectCoreAdminSwitch" name="inject_core_admin" @checked($injectCoreAdmin)>
                    <label class="form-check-label" for="injectCoreAdminSwitch">{{ trans('extended-translation::admin.settings.inject_label') }}</label>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> {{ trans('messages.actions.save') }}
        </button>
    </form>
@endsection
