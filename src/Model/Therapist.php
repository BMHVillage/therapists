<?php

declare(strict_types=1);

namespace BlackMentalHealthVillage\Therapists\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

use function collect;

final class Therapist extends Eloquent
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'contact',
        'statement',
        'location',
        'hash',
        'offersOnlineTherapy',
        'acceptingAppointments',
    ];

    protected $table = 'therapists';

    public function toArray(): array
    {
        return collect(parent::toArray())->only($this->fillable)->toArray();
    }
}
