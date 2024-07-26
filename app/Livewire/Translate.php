<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Livewire\Component;
use LaravelLang\Translator\Services\Translate as Translator;

class Translate extends Component
{
    public Collection $initialItems;
    public Collection $items;
    public Collection $currentItems;
    public Collection $filteredItems;
    public string $language;
    public string $search = '';
    public string $config = 'kirano_translate';
    public string $page;
    public string $pageName;
    public bool $loading = false;
    private Translator $translator;
    private string $main_key = 'kirano_translate_main';

    public function boot(
        Translator $translator,
    ): void
    {
        $this->translator = $translator;
    }

    public function mount(): void
    {
        $this->language = $this->getConfig('default_lang');
        if(empty($this->getConfig('pages', []))) abort(400);

        $this->initialItems = collect();

        $page_index = array_search($this->getConfig('default_page'), array_keys($this->getConfig('pages', [])));
        $this->page = array_keys($this->getConfig('pages', []))[$page_index];
        $this->pageName = array_values($this->getConfig('pages', []))[$page_index];

        $data = [];

        foreach ($this->getConfig('pages', []) as $page => $name) {
            $data[$page] = [
                'ru' => collect($this->compactMain(Lang::get($page, locale: 'ru'))),
                'en' => collect($this->compactMain(Lang::get($page, locale: 'en'))),
                'uz' => collect($this->compactMain(Lang::get($page, locale: 'uz'))),
            ];

            $this->initialItems[$page] = collect([
                'ru' => $this->chunk($data[$page]['ru']),
                'en' => $this->chunk($data[$page]['en']),
                'uz' => $this->chunk($data[$page]['uz']),
            ]);
        }

        $this->items = $this->initialItems;
        $this->currentItems = $this->items[$this->page][$this->language];
        $this->filteredItems = $this->currentItems;
    }

    public function chunk($collection): Collection
    {
        return $collection->chunk(ceil($collection->count() / 3));
    }

    public function compactMain($array): array
    {
        $new_array = [];

        if(is_array($array)) {
            foreach ($array as $key => $value) {
                if(is_array($value)) $new_array[$key] = $value;
                else $new_array[$this->main_key][$key] = $value;
            }
        }

        return $new_array;
    }

    public function revert(): void
    {
        $this->items[$this->page][$this->language] = $this->initialItems[$this->page][$this->language];
    }

    public function translate(): void
    {
        foreach($this->currentItems as $col => $group) {
            foreach($group as $name => $keys) {
                $this->items[$this->page][$this->language][$col][$name] = $this->translator->viaGoogle(
                    $this->items[$this->page][$this->getConfig('auto_translate.from')][$col][$name],
                    $this->language
                );
            }
        }
    }

    public function updatedLanguage(): void
    {
        $this->currentItems = $this->items[$this->page][$this->language];
    }

    public function getConfig($name, $default = null) {
        return config($this->config . '.' . $name, $default);
    }

    public function updatedPage(): void
    {
        $this->pageName = $this->getConfig('pages', [])[$this->page];
        $this->currentItems = $this->items[$this->page][$this->language];
        $this->filteredItems = $this->currentItems;
    }

    public function save(): void {
        $content = "<?php\n\nreturn\n\n[\n";

        foreach($this->items[$this->page][$this->language] as $group) {
            foreach($group as $name => $keys) {
                if($name === $this->main_key) {
                    foreach($keys as $k => $v) {
                        $content .= "\t'".$k."' => '".$v."',\n";
                    }
                } else {
                    $content .= "'$name' => [\n";

                    foreach($keys as $k => $v) {
                        $content .= "\t'".$k."' => '".$v."',\n";
                    }

                    $content .= "],\n";
                }
            }
        }

        $content .= "];";

        file_put_contents(base_path() . "/" . $this->getConfig('lang_dir') . "/$this->language/$this->page.php", $content);
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
        $this->dispatch('resize-textarea');

        return view('livewire.translate');
    }
}
