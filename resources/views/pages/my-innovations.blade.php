@extends('layouts.app')
@section('title', 'My Innovations — AgriTech Pro')
@section('extra_css')<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>@endsection
@section('content')
<div class="section" style="background:var(--bg-2);">
  <div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
      <div><h1 style="font-size:1.5rem;font-weight:700;color:var(--text);letter-spacing:-0.02em;">💡 My Innovations</h1>
      <p style="color:var(--text-muted);">Innovations you've submitted to the community</p></div>
      <a href="{{ route('innovation') }}#submit" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Submit New</a>
    </div>
    <div style="display:grid;gap:16px;">
      @forelse(isset($innovations)?$innovations:[] as $innovation)
        @php $sc=['draft'=>'badge-gray','pending_review'=>'badge-earth','approved'=>'badge-green','featured'=>'badge-sky','rejected'=>'badge-coral']; @endphp
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:22px;display:flex;gap:18px;align-items:flex-start;flex-wrap:wrap;">
          <div style="width:52px;height:52px;border-radius:14px;background:#dcfce7;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;">💡</div>
          <div style="flex:1;min-width:200px;">
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:6px;">
              <span class="badge {{ $sc[$innovation->status]??'badge-gray' }}" style="font-size:.75rem;">{{ ucfirst($innovation->status) }}</span>
              <span class="badge badge-gray" style="font-size:.75rem;">{{ ucwords(str_replace('_',' ',$innovation->category)) }}</span>
              @if($innovation->in_competition)<span class="badge badge-earth" style="font-size:.75rem;">🏆 Competition</span>@endif
            </div>
            <div style="font-size:1.0625rem;font-weight:600;color:var(--text);letter-spacing:-0.01em;margin-bottom:4px;">{{ $innovation->title }}</div>
            <div style="font-size:.8125rem;color:var(--text-muted);line-height:1.5;margin-bottom:10px;">{{ Str::limit($innovation->description,120) }}</div>
            <div style="display:flex;gap:16px;font-size:.8125rem;color:var(--text-muted);flex-wrap:wrap;">
              <span><i class="fas fa-thumbs-up" style="color:var(--primary);"></i> {{ $innovation->vote_count }} votes</span>
              <span><i class="fas fa-eye"></i> {{ $innovation->view_count }} views</span>
              <span><i class="fas fa-clock"></i> {{ $innovation->created_at->format('M j, Y') }}</span>
            </div>
            @if($innovation->status==='rejected'&&$innovation->rejection_reason)
              <div style="margin-top:10px;background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius-md);padding:8px 12px;font-size:.8125rem;color:#dc2626;"><i class="fas fa-info-circle"></i> {{ $innovation->rejection_reason }}</div>
            @endif
          </div>
          <div>
            @if(in_array($innovation->status,['approved','featured']))
              <a href="{{ route('innovation.show',$innovation) }}" class="btn btn-outline btn-sm"><i class="fas fa-eye"></i> View Public</a>
            @endif
          </div>
        </div>
      @empty
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:60px;text-align:center;">
          <i class="fas fa-lightbulb" style="font-size:3rem;color:var(--text-muted);margin-bottom:16px;display:block;opacity:.3;"></i>
          <h3 style="font-size:1.25rem;font-weight:600;letter-spacing:-0.01em;margin-bottom:8px;">No innovations yet</h3>
          <p style="color:var(--text-muted);margin-bottom:20px;">Share your farming innovation with 12,000+ farmers.</p>
          <a href="{{ route('innovation') }}#submit" class="btn btn-primary btn-lg"><i class="fas fa-lightbulb"></i> Submit Your First Innovation</a>
        </div>
      @endforelse
    </div>
    @if(isset($innovations)&&$innovations->hasPages())<div style="margin-top:24px;">{{ $innovations->links() }}</div>@endif
  </div>
</div>
@include('partials.footer')
@endsection
