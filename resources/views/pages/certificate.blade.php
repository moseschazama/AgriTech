@extends('layouts.app')
@section('title', 'Certificate — AgriTech Pro')
@section('extra_css')
<style>
@media print{.no-print{display:none!important;}.cert-wrapper{box-shadow:none;}}
@media(max-width:768px){.cert-wrapper{padding:30px 16px;border-width:6px;}.cert-inner{padding:24px 16px;}.cert-name{font-size:1.8rem;padding:0 16px 6px;}.cert-course{font-size:1.1rem;}.cert-meta-row{gap:24px;}.cert-meta-val{font-size:.82rem;}.cert-meta-lbl{font-size:.65rem;}.cert-seal{width:60px;height:60px;right:20px;bottom:20px;}.cert-seal i{font-size:1.2rem;}.cert-seal div{font-size:.45rem;}.cert-org{font-size:1.1rem;}.cert-tagline{font-size:.7rem;letter-spacing:.06em;}.cert-body-text{font-size:.88rem;}}
.cert-wrapper{max-width:860px;margin:40px auto;background:#fff;border:12px solid #0d4a1e;border-radius:4px;padding:60px;position:relative;box-shadow:0 20px 60px rgba(0,0,0,.2);}
.cert-inner{border:3px solid #16a34a;padding:40px;text-align:center;}
.cert-logo{font-size:3rem;margin-bottom:8px;}
.cert-org{font-family:var(--font-display);font-size:1.5rem;font-weight:800;color:#0d4a1e;letter-spacing:.05em;margin-bottom:6px;}
.cert-tagline{font-size:.82rem;color:#64748b;letter-spacing:.12em;text-transform:uppercase;margin-bottom:40px;}
.cert-body-text{font-size:1rem;color:#475569;line-height:1.8;margin-bottom:16px;}
.cert-name{font-family:Georgia,serif;font-size:3rem;color:#0d4a1e;font-style:italic;border-bottom:2px solid #0d4a1e;display:inline-block;padding:0 40px 8px;margin:16px 0 24px;}
.cert-course{font-family:var(--font-display);font-size:1.6rem;font-weight:800;color:#1e293b;margin-bottom:20px;line-height:1.3;}
.cert-meta-row{display:flex;justify-content:center;gap:60px;margin:32px 0;flex-wrap:wrap;}
.cert-meta-item{text-align:center;}
.cert-meta-val{font-family:var(--font-display);font-size:.95rem;font-weight:700;color:#0d4a1e;}
.cert-meta-lbl{font-size:.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-top:2px;}
.cert-number{font-family:var(--font-mono,monospace);font-size:.78rem;color:#94a3b8;margin-top:28px;}
.cert-seal{position:absolute;right:70px;bottom:70px;width:90px;height:90px;border-radius:50%;border:4px solid #16a34a;display:flex;align-items:center;justify-content:center;flex-direction:column;background:linear-gradient(135deg,#f0fdf4,#dcfce7);}
</style>
@endsection

@section('content')
<div class="section no-print" style="background:var(--bg-2);padding-bottom:0;">
  <div class="container" style="max-width:900px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0;flex-wrap:wrap;gap:12px;">
      <a href="{{ route('profile').'#tab-certs' }}" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back to Certificates</a>
      <div style="display:flex;gap:8px;">
        <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="fas fa-print"></i> Print / Save PDF</button>
        <button onclick="shareToLinkedIn()" class="btn btn-outline btn-sm"><i class="fab fa-linkedin"></i> Share on LinkedIn</button>
      </div>
    </div>
  </div>
</div>

<div style="background:var(--bg-2);padding:0 20px 60px;">
  <div class="cert-wrapper">
    <div class="cert-inner">
      <div class="cert-logo">🌱</div>
      <div class="cert-org">AgriTech Pro</div>
      <div class="cert-tagline">Smart Agriculture Platform · Malawi & Zambia</div>
      <div class="cert-body-text">This is to certify that</div>
      <div class="cert-name">{{ isset($enrollment) ? $enrollment->user->full_name : Auth::user()->full_name }}</div>
      <div class="cert-body-text">has successfully completed the course</div>
      <div class="cert-course">{{ isset($enrollment) ? $enrollment->course->title : 'Course Title' }}</div>
      <div class="cert-body-text" style="font-size:.88rem;">
        demonstrating proficiency in {{ isset($enrollment) ? ucwords(str_replace('_',' ',$enrollment->course->category)) : 'Agriculture' }}<br/>
        and a commitment to modern farming excellence.
      </div>
      <div class="cert-meta-row">
        <div class="cert-meta-item">
          <div class="cert-meta-val">{{ isset($enrollment) ? $enrollment->course->total_lessons.' Lessons' : '—' }}</div>
          <div class="cert-meta-lbl">Completed</div>
        </div>
        <div class="cert-meta-item">
          <div class="cert-meta-val">{{ isset($enrollment) ? ($enrollment->course->total_duration_minutes>0 ? intdiv($enrollment->course->total_duration_minutes,60).'h '.($enrollment->course->total_duration_minutes%60).'m' : '—') : '—' }}</div>
          <div class="cert-meta-lbl">Course Duration</div>
        </div>
        <div class="cert-meta-item">
          <div class="cert-meta-val">{{ isset($enrollment) ? $enrollment->completed_at?->format('M j, Y') : now()->format('M j, Y') }}</div>
          <div class="cert-meta-lbl">Completion Date</div>
        </div>
        <div class="cert-meta-item">
          <div class="cert-meta-val">100%</div>
          <div class="cert-meta-lbl">Final Score</div>
        </div>
      </div>
      <div style="display:flex;justify-content:center;gap:80px;margin-top:28px;padding-top:20px;border-top:1px solid #e2e8f0;">
        <div style="text-align:center;">
          <div style="font-family:Georgia,serif;font-style:italic;font-size:1.2rem;color:#0d4a1e;border-bottom:1px solid #0d4a1e;padding-bottom:4px;margin-bottom:6px;">AgriTech Pro</div>
          <div style="font-size:.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">Platform Director</div>
        </div>
        <div style="text-align:center;">
          <div style="font-family:Georgia,serif;font-style:italic;font-size:1.2rem;color:#0d4a1e;border-bottom:1px solid #0d4a1e;padding-bottom:4px;margin-bottom:6px;">
            {{ isset($enrollment) ? $enrollment->course->instructor->name??'Instructor' : 'Instructor' }}
          </div>
          <div style="font-size:.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">Course Instructor</div>
        </div>
      </div>
      <div class="cert-number">Certificate ID: {{ isset($enrollment) ? $enrollment->certificate_number : 'CERT-XXXXXXXX' }}</div>
    </div>
    <div class="cert-seal">
      <i class="fas fa-certificate" style="color:#16a34a;font-size:1.8rem;"></i>
      <div style="font-size:.55rem;font-weight:800;color:#0d4a1e;text-align:center;line-height:1.3;margin-top:3px;">VERIFIED<br>CERTIFICATE</div>
    </div>
  </div>
</div>

@include('partials.footer')
@endsection
@section('extra_js')
<script>
function shareToLinkedIn() {
  const certNum = '{{ isset($enrollment) ? $enrollment->certificate_number : "" }}';
  const courseTitle = '{{ isset($enrollment) ? addslashes($enrollment->course->title) : "" }}';
  const url = `https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME&name=${encodeURIComponent(courseTitle)}&organizationName=AgriTech+Pro&certId=${encodeURIComponent(certNum)}&certUrl=${encodeURIComponent(window.location.href)}`;
  window.open(url, '_blank', 'width=600,height=500');
}
</script>
@endsection
