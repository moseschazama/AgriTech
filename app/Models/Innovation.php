<?php
// app/Models/Innovation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Innovation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id','title','slug','description','category','status',
        'impact_summary','estimated_cost','implementation_steps',
        'district','images','video_url','in_competition',
        'competition_id','rejection_reason','reviewed_by','reviewed_at',
    ];

    protected $casts = [
        'images'         => 'array',
        'in_competition' => 'boolean',
        'reviewed_at'    => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (Innovation $i) {
            $i->slug = $i->slug ?: Str::slug($i->title) . '-' . Str::random(6);
        });
    }

    // ── Relationships ─────────────────────────────────────────────────

    public function user(): BelongsTo         { return $this->belongsTo(User::class); }
    public function competition(): BelongsTo  { return $this->belongsTo(Competition::class); }
    public function reviewer(): BelongsTo     { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function votes(): HasMany          { return $this->hasMany(InnovationVote::class); }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved')->orWhere('status', 'featured');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending_review');
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeTopVoted($query)
    {
        return $query->orderByDesc('vote_count');
    }

    public function scopeInCompetition($query, int $competitionId)
    {
        return $query->where('competition_id', $competitionId)->where('in_competition', true);
    }

    // ── Business Logic ────────────────────────────────────────────────

    /**
     * Toggle a user's vote on this innovation. Returns true if vote added.
     */
    public function toggleVote(User $user): bool
    {
        $existing = $this->votes()->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
            $this->decrement('vote_count');
            return false;
        }

        $this->votes()->create(['user_id' => $user->id]);
        $this->increment('vote_count');
        return true;
    }

    /**
     * Admin approves the innovation.
     */
    public function approve(User $admin): void
    {
        $this->update([
            'status'      => 'approved',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        Notification::create([
            'user_id'    => $this->user_id,
            'title'      => 'Innovation Approved! 🎉',
            'message'    => "Your innovation \"{$this->title}\" has been approved and is now live.",
            'type'       => 'innovation',
            'icon'       => 'fas fa-lightbulb',
            'icon_color' => 'var(--earth-500)',
        ]);
    }

    /**
     * Admin rejects the innovation with a reason.
     */
    public function reject(User $admin, string $reason): void
    {
        $this->update([
            'status'            => 'rejected',
            'reviewed_by'       => $admin->id,
            'reviewed_at'       => now(),
            'rejection_reason'  => $reason,
        ]);

        Notification::create([
            'user_id'    => $this->user_id,
            'title'      => 'Innovation Needs Changes',
            'message'    => "Your innovation \"{$this->title}\" was not approved. Reason: {$reason}",
            'type'       => 'innovation',
            'icon'       => 'fas fa-exclamation-circle',
            'icon_color' => 'var(--danger)',
        ]);
    }

    public function recordView(): void
    {
        $this->increment('view_count');
    }
}
