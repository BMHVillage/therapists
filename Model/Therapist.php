<?php
declare(strict_types=1);

namespace Ghostwriter\TnTherapists\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Therapist extends Eloquent
{
use SoftDeletes;
    protected $table = 'therapists';

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
    protected $casts = [
        'id' => 'integer',
    ];
}
