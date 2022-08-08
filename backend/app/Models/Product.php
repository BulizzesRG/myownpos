<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, Searchable, SoftDeletes;
	protected $guarded = [];

	/**
	 * Get the indexable data array for the model.
	 */
	public function toSearchableArray()
	{
		$properties = $this->toArray();

		$propertiesToFind = [
			'id' => $properties['id'],
			'description' => $properties['description'],
			'barcode' => $properties['barcode'],
			'alternative_code' => $properties['alternative_code']
		];

		return $propertiesToFind;
	}
}
