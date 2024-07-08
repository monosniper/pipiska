<div>
    <header class="header">
        <div class="header__item brand">
            Kirano Translate | {{ config('app.name') }}
        </div>

        <div class="header__item header__block_rounded header__buttons">
            <label for="search" class="header__block_rounded header__block search">
                <input wire:model.live="search" placeholder="Поиск..." id="search" type="search">
                @include('inc.icon', ['name' => 'search'])
            </label>
            <div class="header__block header__block_rounded header__button">
                @include('inc.icon', ['name' => 'translate'])
            </div>
            <div class="header__block header__block_rounded header__button">
                @include('inc.icon', ['name' => 'revert'])
            </div>
            <div class="header__block header__block_rounded header__button">
                @include('inc.icon', ['name' => 'check'])
            </div>
        </div>
    </header>

    <div class="header__block_rounded header__block languages">
        <div class="languages__item active">Русский</div>
        <div class="languages__item">English</div>
        <div class="languages__item">Uzbek</div>
    </div>

    <div class="fluid-container columns">
        @foreach($items[$language] as $col => $groups)
            @isset($filteredItems[$col])
                <div class="column" wire:transition>
                    @foreach($groups as $name => $group)
                        @isset($filteredItems[$col][$name])
                            <div data-title="{{ $name }}" class="block" wire:transition>
                                @foreach($group as $key => $value)
                                    @isset($filteredItems[$col][$name][$key])
                                        <div class="item" wire:transition>
                                            <label for="{{ $name . '.' . $key }}" class="item__key">{{ $key }}</label>
                                            <div class="item__value">
                                                <textarea name="{{ $name . '.' . $key }}" id="{{ $name . '.' . $key }}">{{ $value }}</textarea>
                                            </div>
                                        </div>
                                    @endisset
                                @endforeach
                            </div>
                        @endisset
                    @endforeach
                </div>
            @endisset
        @endforeach
    </div>
</div>
