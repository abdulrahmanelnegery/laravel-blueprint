<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\SlugGenerator;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'email',
        'is_active',
    ];

    public function slug(): string
    {
        return (new SlugGenerator)->generate($this->name);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
