<div>
    <header class="header">
        @if($loading)
            <div class="header__disable"></div>
        @endif

        <div class="header__item brand">
            Kirano Translate | {{ config('app.name') }}
        </div>

        <div class="header__item header__block_rounded header__buttons">
            <label for="search" class="header__block_rounded header__block search">
                <input wire:model.live="search" placeholder="Поиск..." id="search" type="search">
                @include('inc.icon', ['name' => 'search'])
            </label>
            @if($language !== 'ru')
                <div id="translate" class="header__block header__block_rounded header__button">
                    @if($loading)
                        <span class="loader"></span>
                    @else
                        @include('inc.icon', ['name' => 'translate'])
                    @endif
                </div>
            @endif
            <div wire:click="revert" class="header__block header__block_rounded header__button">
                @include('inc.icon', ['name' => 'revert'])
            </div>
            <div class="header__block header__block_rounded header__button">
                @include('inc.icon', ['name' => 'check'])
            </div>
        </div>

        <div class="header__block_rounded header__block languages">
            <div data-lang="ru" @class(['languages__item', $language === 'ru' ? 'active' : ''])>Русский</div>
            <div data-lang="en" @class(['languages__item', $language === 'en' ? 'active' : ''])>English</div>
            <div data-lang="uz" @class(['languages__item', $language === 'uz' ? 'active' : ''])>Uzbek</div>
        </div>
    </header>

    <div class="fluid-container columns">
        @foreach($currentItems as $col => $groups)
            @isset($filteredItems[$col])
                <div class="column">
                    @foreach($groups as $name => $group)
                        @isset($filteredItems[$col][$name])
                            <div data-title="{{ $name }}" class="block" >
                                @foreach($group as $key => $value)
                                    @isset($filteredItems[$col][$name][$key])
                                        <div class="item" >
                                            <label for="{{ $name . '.' . $key }}" class="item__key">{{ $key }}</label>
                                            <div class="item__value">
                                                <textarea
                                                    @disabled($loading)
                                                    style="display: {{ $language === 'ru' ? 'block' : 'none' }}"
                                                    wire:model.live="items.ru.{{ $col }}.{{ $name }}.{{ $key }}"
                                                    name="{{ $name . '.' . $key }}"
                                                    id="{{ $name . '.' . $key }}"
                                                ></textarea>
                                                <textarea
                                                    @disabled($loading)
                                                    style="display: {{ $language === 'en' ? 'block' : 'none' }}"
                                                    wire:model.live="items.en.{{ $col }}.{{ $name }}.{{ $key }}"
                                                    name="{{ $name . '.' . $key }}"
                                                    id="{{ $name . '.' . $key }}"
                                                ></textarea>
                                                <textarea
                                                    @disabled($loading)
                                                    style="display: {{ $language === 'uz' ? 'block' : 'none' }}"
                                                    wire:model.live="items.uz.{{ $col }}.{{ $name }}.{{ $key }}"
                                                    name="{{ $name . '.' . $key }}"
                                                    id="{{ $name . '.' . $key }}"
                                                ></textarea>
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

    <script>
        document.querySelectorAll('.languages__item').forEach(item => {
            item.addEventListener('click', () => {
                if(!item.classList.contains('active')) {
                    document.querySelector('.languages__item.active').classList.remove('active')
                    item.classList.add('active')
                    @this.set('language', item.getAttribute('data-lang'));
                }
            })
        });

        const stopLoading = () => {
            setTimeout(() => @this.set('loading', false), 5000)
        }

        document.querySelector('#translate').addEventListener('click', () => {
            @this.set('loading', true);
            @this.translate()

            stopLoading()
        });
    </script>
</div>
