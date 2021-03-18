<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GenresHasCategoriesRule implements Rule
{

    private $categoriesId;
    private $genresId;

    public function __construct(array $categoriesId)
    {
        $this->categoriesId = is_array($categoriesId) ? array_unique($categoriesId) : [];
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->genresId = is_array($value) ? array_unique($value) : [];
        if (!count($this->genresId) || !count($this->categoriesId)) {
            return false;
        }

        $categoriesFound = [];
        foreach ($this->genresId as $genreId) {
            $rows = $this->getRows($genreId);
            if (!$rows->count()) {
                return false;
            }
            array_push($categoriesFound, ...$rows->pluck('category_id')->toArray());
        }
        if (count($categoriesFound) !== count($this->categoriesId)) {
            return false;
        }

        return true;
    }

    protected function getRows($genreId): Collection
    {
        return DB::table('category_genre')->where('genre_id', $genreId)->whereIn('category_id', $this->categoriesId)->get();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        // return 'A genre ID must be related at least a category ID';
        return trans('validation.genres_has_categories');
    }
}
