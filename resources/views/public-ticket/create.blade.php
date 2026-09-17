<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $siteTitle }}</title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: #f3f4f6;
            color: #111827;
        }
        .wrapper {
            max-width: 640px;
            margin: 0 auto;
            padding: 2.5rem 1.25rem 4rem;
        }
        h1 { font-size: 1.5rem; margin-bottom: .25rem; }
        p.subtitle { color: #6b7280; margin-top: 0; margin-bottom: 1.5rem; }
        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            padding: 1.75rem;
            box-shadow: 0 1px 2px rgba(0,0,0,.04);
        }
        .field { margin-bottom: 1.1rem; }
        label { display: block; font-weight: 600; font-size: .875rem; margin-bottom: .35rem; }
        input, select, textarea {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: .5rem;
            padding: .6rem .75rem;
            font-size: .95rem;
            font-family: inherit;
        }
        textarea { min-height: 130px; resize: vertical; }
        .row { display: flex; gap: 1rem; }
        .row .field { flex: 1; }
        button {
            background: #1d4ed8;
            color: #fff;
            border: 0;
            border-radius: .5rem;
            padding: .7rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
        }
        button:hover { background: #1e40af; }
        .alert-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 1rem 1.25rem;
            border-radius: .5rem;
            margin-bottom: 1.5rem;
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 1rem 1.25rem;
            border-radius: .5rem;
            margin-bottom: 1.5rem;
        }
        .alert-error ul { margin: .25rem 0 0; padding-left: 1.1rem; }
        .disabled-notice {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            padding: 2rem;
            text-align: center;
            color: #6b7280;
        }
        .error-text { color: #dc2626; font-size: .8rem; margin-top: .25rem; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>{{ $siteTitle }}</h1>
        <p class="subtitle">{{ __('Open a support ticket') }}</p>

        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if (!$enabled)
            <div class="disabled-notice">
                {{ __('Public ticket creation is currently unavailable.') }}
            </div>
        @else
            @if ($errors->any())
                <div class="alert-error">
                    <strong>{{ __('Please check the fields below:') }}</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <form method="POST" action="{{ route('public-ticket.store') }}">
                    @csrf

                    <div class="row">
                        <div class="field">
                            <label for="name">{{ __('Name') }}</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="255">
                        </div>
                        <div class="field">
                            <label for="email">{{ __('Email') }}</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="255">
                        </div>
                    </div>

                    <div class="field">
                        <label for="phone">{{ __('Phone') }}</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" maxlength="50">
                    </div>

                    <div class="row">
                        <div class="field">
                            <label for="unit_id">{{ __('Unit') }}</label>
                            <select id="unit_id" name="unit_id" required>
                                <option value="">{{ __('Select an option') }}</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}" @selected(old('unit_id') == $unit->id)>{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="category_id">{{ __('Category') }}</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">{{ __('Select an option') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <label for="title">{{ __('Subject') }}</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required maxlength="255">
                    </div>

                    <div class="field">
                        <label for="message">{{ __('Message') }}</label>
                        <textarea id="message" name="message" required>{{ old('message') }}</textarea>
                    </div>

                    <button type="submit">{{ __('Submit') }}</button>
                </form>
            </div>

            <script>
                const categories = @json($categoriesForSelect);
                const oldCategoryId = @json(old('category_id'));

                const unitSelect = document.getElementById('unit_id');
                const categorySelect = document.getElementById('category_id');

                function refreshCategories() {
                    const unitId = parseInt(unitSelect.value, 10);
                    categorySelect.innerHTML = '<option value="">{{ __('Select an option') }}</option>';

                    categories
                        .filter((category) => !unitId || category.unit_ids.includes(unitId))
                        .forEach((category) => {
                            const option = document.createElement('option');
                            option.value = category.id;
                            option.textContent = category.name;
                            if (oldCategoryId && oldCategoryId == category.id) {
                                option.selected = true;
                            }
                            categorySelect.appendChild(option);
                        });
                }

                unitSelect.addEventListener('change', refreshCategories);
                refreshCategories();
            </script>
        @endif
    </div>
</body>
</html>
