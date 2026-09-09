<?php
// app/Models/Competition.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competition extends Model
{
    protected $fillable = [
        'title','description','rules','status','first_prize','second_prize',
        'third_prize','entry_fee','starts_at','ends_at','max_entries',
        'entry_count','banner_image',
    ];

    protected $casts = [
        'first_prize'  => 'decimal:2',
        'second_prize' => 'decimal:2',
        'third_prize'  => 'decimal:2',
        'entry_fee'    => 'decimal:2',
        'starts_at'    => 'date',
        'ends_at'      => 'date',
    ];

    public function innovations(): HasMany
    {
        return $this->hasMany(Innovation::class);
    }

    /** Is this competition currently accepting entries? */
    public function isOpen(): bool
    {
        return $this->status === 'active'
            && now()->between($this->starts_at, $this->ends_at)
            && (!$this->max_entries || $this->entry_count < $this->max_entries);
    }

    public function daysRemaining(): int
    {
        return max(0, now()->diffInDays($this->ends_at, false));
    }

    /**
     * Pick winners by vote count, persist their ranking and notify them.
     * Run after ends_at via scheduled job or manually from admin panel.
     */
    public function selectWinners(): array
    {
        $winners = $this->innovations()
                         ->approved()
                         ->orderByDesc('vote_count')
                         ->orderByDesc('view_count')
                         ->take(3)
                         ->get();

        $prizes = [$this->first_prize, $this->second_prize, $this->third_prize];

        foreach ($winners as $i => $innovation) {
            $innovation->update([
                'winner_position' => $i + 1,
                'winner_prize'    => $prizes[$i],
                'won_at'          => now(),
            ]);

            Notification::create([
                'user_id'    => $innovation->user_id,
                'title'      => '🏆 You Won the Competition!',
                'message'    => "Congratulations! \"{$innovation->title}\" placed #" . ($i + 1) .
                                 " and won " . number_format($prizes[$i]) . " MWK.",
                'type'       => 'innovation',
                'icon'       => 'fas fa-trophy',
                'icon_color' => 'var(--earth-500)',
            ]);
        }

        $this->update(['status' => 'closed']);
        return $winners->toArray();
    }

    /**
     * Competition entries with performance data — used for results lists and CSV export.
     */
    public function entriesWithPerformance()
    {
        return $this->innovations()
            ->with('user')
            ->get()
            ->map(function ($innovation) {
                return [
                    'position'       => $innovation->winner_position,
                    'prize'          => $innovation->winner_prize,
                    'title'          => $innovation->title,
                    'category'       => $innovation->category,
                    'farmer'         => $innovation->user->full_name ?? 'Farmer',
                    'district'       => $innovation->district ?? '-',
                    'votes'          => $innovation->vote_count ?? 0,
                    'views'          => $innovation->view_count ?? 0,
                    'impact'         => $innovation->impact_summary ?? '-',
                    'submitted_at'   => $innovation->created_at?->format('Y-m-d'),
                ];
            })
            ->sortByDesc(function ($row) {
                return $row['votes'];
            })
            ->values();
    }

    /**
     * Winners of a previous round, ranked 1-3.
     */
    public function winners()
    {
        return $this->innovations()->winners()->with('user')->get();
    }
}
