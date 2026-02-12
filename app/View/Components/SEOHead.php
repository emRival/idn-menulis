<?php

namespace App\View\Components;

use App\Services\SEOService;
use Illuminate\View\Component;
use Illuminate\View\View;

class SEOHead extends Component
{
    public SEOService $seo;
    public string $title;
    public string $description;
    public ?string $image;
    public ?string $canonical;
    public ?string $type;
    public array $schemas;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $title = '',
        string $description = '',
        ?string $image = null,
        ?string $canonical = null,
        string $type = 'website',
        array $schemas = []
    ) {
        $this->seo = app(SEOService::class);
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->canonical = $canonical;
        $this->type = $type;
        $this->schemas = $schemas;

        // Build SEO
        if (!empty($title)) {
            $this->seo->setTitle($title);
        }

        if (!empty($description)) {
            $this->seo->setDescription($description);
        }

        if (!empty($image)) {
            $this->seo->setImage($image);
        }

        if (!empty($canonical)) {
            $this->seo->setCanonical($canonical);
        }

        $this->seo->setType($type);

        foreach ($schemas as $schema) {
            $this->seo->addSchema($schema);
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.seo-head', [
            'seoHtml' => $this->seo->render(),
        ]);
    }
}
