<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MetaRessource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'pagination' => [
                'currentPage' => $this->currentPage(),
                'totalPages' => $this->lastPage(),
                'totalItems' => $this->total(),
                'itemsPerPage' => $this->perPage(),
                'hasNext' => $this->hasMorePages(),
                'hasPrevious' => $this->onFirstPage() === false,

            ],
            'links' => [
                'self' => $this->url($this->currentPage()),
                'next' => $this->nextPageUrl(),
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
            ],
        ];
    }
}
