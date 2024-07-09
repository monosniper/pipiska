<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use LaravelLang\LocaleList\Locale;
use Livewire\Component;
use LaravelLang\Translator\Services\Translate as Translator;

class Translate extends Component
{
    public Collection $initialItems;
    public Collection $items;
    public Collection $currentItems;
    public Collection $filteredItems;
    public string $language = 'ru';
    public string $search = '';
    public bool $loading = false;
    public string $translateFile = 'test_validation';
    private Translator $translator;

    public function boot(
        Translator $translator,
    ): void
    {
        $this->translator = $translator;
    }

    public function mount(): void
    {
        $data = [
            'ru' => collect(Lang::get($this->translateFile, locale: 'ru')),
            'en' => collect(Lang::get($this->translateFile, locale: 'en')),
            'uz' => collect(Lang::get($this->translateFile, locale: 'uz')),
        ];

        $this->initialItems = collect([
            'ru' => $this->chunk($data['ru']),
            'en' => $this->chunk($data['en']),
            'uz' => $this->chunk($data['uz']),
        ]);

        $this->items = $this->initialItems;
        $this->currentItems = $this->items[$this->language];
        $this->filteredItems = $this->currentItems;
    }

    public function chunk($collection)
    {
        return $collection->chunk(ceil($collection->count() / 3));
    }

    public function revert(): void
    {
        $this->items[$this->language] = $this->initialItems[$this->language];
    }

    public function translate(): void
    {
        $this->loading = true;

        foreach($this->currentItems as $col => $group) {
            foreach($group as $name => $keys) {
                $this->items[$this->language][$col][$name] = $this->translator->viaGoogle(
                    $this->items['ru'][$col][$name],
                    $this->language
                );
            }
        }

        $this->loading = false;
    }

    public function updatedLanguage(): void
    {
        $this->currentItems = $this->items[$this->language];
    }

    public function updatedSearch(): void
    {
        if (empty($this->search)) {
            $this->filteredItems = $this->currentItems;
        } else {
            $items = [];

            foreach($this->currentItems as $group) {
                foreach($group as $name => $keys) {
                    foreach($keys as $k => $v) {
                        if(stristr($k, $this->search) || stristr($v, $this->search) || stristr($name, $this->search)) {
                            if(isset($items[$name])) $items[$name][$k] = $v;
                            else $items[$name][$k] = $v;
                        }
                    }
                }
            }

            $this->filteredItems = $this->chunk(collect($items));
        }
    }

    public function render(): View
    {
        return view('livewire.translate');
    }
}
