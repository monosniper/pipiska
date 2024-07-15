<div>
    <header class="header">
        <div class="header__disable"></div>

        <div class="header__item">
            <div class="brand">
                Kirano Translate | {{ config('app.name') }}
            </div>
            <div class="pages">
                <div class="dropdown header__block_rounded">
                    <div class="select">
                        <span>{{ $pageName }}</span>
                        <i class="fa fa-chevron-left"></i>
                    </div>
                    <ul class="dropdown-menu">
                        @foreach(config($config . '.pages', []) as $p => $n)
                            <li id="{{ $p }}">{{ $n }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="header__item header__block_rounded header__buttons">
            <label for="search" class="header__block_rounded header__block search">
                <input wire:model.live="search" placeholder="Поиск..." id="search" type="search">
                @include('inc.icon', ['name' => 'search'])
            </label>
            <div
                style="display: {{ (!in_array($language, $this->getConfig('auto_translate.exclude_languages'))
                && !in_array($page, $this->getConfig('auto_translate.exclude_pages'))) ? 'flex' : 'none' }}"
                id="js-translate"
                class="header__block header__block_rounded header__button"
            >
                <span class="loader"></span>
                @include('inc.icon', ['name' => 'translate'])
            </div>
            @if($this->getConfig('can_revert'))
                <div wire:click="revert" class="header__block header__block_rounded header__button">
                    @include('inc.icon', ['name' => 'revert'])
                </div>
            @endif
            <div wire:click="save" class="header__block header__block_rounded header__button">
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
                            <div data-title="{{ config($config.'.groups.'.$name, $name) }}" class="block">
                                @foreach($group as $key => $value)
                                    @isset($filteredItems[$col][$name][$key])
                                        <div class="item" >
                                            <label for="{{ $name . '.' . $key }}" class="item__key">{{ $key }}</label>
                                            <div class="item__value">
                                                <textarea
                                                    @disabled($loading)
                                                    style="display: {{ $language === 'ru' ? 'block' : 'none' }}"
                                                    wire:model.live="items.{{ $page }}.ru.{{ $col }}.{{ $name }}.{{ $key }}"
                                                    name="{{ $name . '.' . $key }}"
                                                    id="{{ $name . '.' . $key }}"
                                                ></textarea>
                                                <textarea
                                                    @disabled($loading)
                                                    style="display: {{ $language === 'en' ? 'block' : 'none' }}"
                                                    wire:model.live="items.{{ $page }}.en.{{ $col }}.{{ $name }}.{{ $key }}"
                                                    name="{{ $name . '.' . $key }}"
                                                    id="{{ $name . '.' . $key }}"
                                                ></textarea>
                                                <textarea
                                                    @disabled($loading)
                                                    style="display: {{ $language === 'uz' ? 'block' : 'none' }}"
                                                    wire:model.live="items.{{ $page }}.uz.{{ $col }}.{{ $name }}.{{ $key }}"
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
        document.addEventListener('DOMContentLoaded', () => {

            const header__disabled = document.querySelector('.header__disable');
            const dropdown = document.querySelector('.dropdown');
            const menu = dropdown.querySelector('.dropdown-menu')
            const translate = document.querySelector('#js-translate');

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
                header__disabled.style.display = 'none'
                translate.querySelector('.loader').style.display = 'none'
                translate.querySelector('svg').style.display = 'block'
            }

            translate.addEventListener('click', () => {
                header__disabled.style.display = 'block'
                translate.querySelector('.loader').style.display = 'inline-block'
                translate.querySelector('svg').style.display = 'none'

                document.querySelectorAll('textarea')
                    .forEach(textarea => textarea.setAttribute('disabled', true))

                @this.translate().finally(() => stopLoading(translate));
            })

            dropdown.addEventListener('click', () => {
                dropdown.setAttribute('tabindex', 1);
                dropdown.focus();

                dropdown.classList.toggle('active')

                if (menu.classList.contains('dropdown-menu--active')) {
                    menu.classList.remove('dropdown-menu--active');
                    menu.style.maxHeight = 0;
                } else {
                    menu.classList.add('dropdown-menu--active');
                    menu.style.maxHeight = menu.scrollHeight + 'px';
                }
            })

            dropdown.addEventListener('focusout', () => {
                dropdown.classList.remove('active')
                menu.classList.remove('dropdown-menu--active');
                menu.style.maxHeight = 0;
            })

            dropdown.querySelectorAll('.dropdown-menu li').forEach(li => {
                li.addEventListener('click', () => {
                    dropdown.querySelector('span').innerText = li.innerText
                    @this.set('page', li.getAttribute('id'));
                })
            })

        })
    </script>

    <style>
        body {
            background: {{ $this->getConfig('theme.background') }};
            color: {{ $this->getConfig('theme.text') }};
        }

        .header, .languages__item.active {
            background: {{ $this->getConfig('theme.accent') }} !important;
        }

        .item:focus-within .item__key {
            background: {{ $this->getConfig('theme.accent') }};
            color: {{ $this->getConfig('theme.group_text') }};
        }

        .item__key {
            background: {{ $this->getConfig('theme.field_background') }};
            color: {{ $this->getConfig('theme.field_color') }};
        }

        textarea {
            background: {{ $this->getConfig('theme.field_background') }};
            color: {{ $this->getConfig('theme.field_text') }};
        }
    </style>
</div>
