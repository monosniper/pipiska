<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Livewire\Component;

class Translate extends Component
{
    public Collection $initialItems;
    public Collection $items;
    public Collection $filteredItems;
    public string $language = 'ru';
    public string $search = '';
    public string $translateFile = 'test_validation';

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
    }

    public function chunk($collection)
    {
        return $collection->chunk(ceil($collection->count() / 3));
    }

    public function render(): View
    {
        $this->items = $this->initialItems;

        if (empty($this->search)) {
            $this->filteredItems = $this->items[$this->language];
        } else {
            $items = [];

            foreach($this->initialItems[$this->language] as $group) {
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

        return view('livewire.translate');
    }
}
