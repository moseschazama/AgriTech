<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  @page { size: A4 landscape; margin: 0; }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Georgia, 'Times New Roman', serif; background: #fff; }

  .outer { width: 1120px; height: 790px; margin: 0 auto; border: 10px solid #0d4a1e; padding: 8px; position: relative; }
  .inner { border: 2px solid #16a34a; padding: 40px 60px; text-align: center; height: 100%; position: relative; }

  .logo { font-size: 42px; margin-bottom: 6px; }
  .org-name { font-family: Georgia, serif; font-size: 28px; font-weight: 800; color: #0d4a1e; letter-spacing: 4px; margin-bottom: 4px; }
  .tagline { font-size: 11px; color: #64748b; letter-spacing: 4px; text-transform: uppercase; margin-bottom: 36px; }

  .cert-text { font-size: 15px; color: #475569; line-height: 1.6; margin-bottom: 14px; }

  .student-name { font-family: Georgia, serif; font-size: 38px; color: #0d4a1e; font-style: italic; border-bottom: 2px solid #0d4a1e; display: inline-block; padding: 0 30px 8px; margin: 10px 0 20px; }

  .course-title { font-size: 22px; font-weight: 800; color: #1e293b; margin-bottom: 18px; line-height: 1.3; }

  .proficiency { font-size: 13px; color: #475569; line-height: 1.6; margin-bottom: 24px; }

  .meta-row { display: flex; justify-content: center; gap: 70px; margin: 24px 0; }
  .meta-item { text-align: center; }
  .meta-val { font-size: 14px; font-weight: 700; color: #0d4a1e; }
  .meta-lbl { font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }

  .signatures { display: flex; justify-content: center; gap: 100px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0; }
  .sig-block { text-align: center; }
  .sig-name { font-family: Georgia, serif; font-style: italic; font-size: 16px; color: #0d4a1e; border-bottom: 1px solid #0d4a1e; padding-bottom: 4px; margin-bottom: 5px; }
  .sig-title { font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }

  .cert-id { font-size: 11px; color: #94a3b8; margin-top: 22px; letter-spacing: 1px; }

  .seal { position: absolute; right: 50px; bottom: 50px; width: 80px; height: 80px; border-radius: 50%; border: 3px solid #16a34a; display: flex; align-items: center; justify-content: center; flex-direction: column; background: #f0fdf4; }
  .seal-icon { font-size: 24px; color: #16a34a; }
  .seal-text { font-size: 7px; font-weight: 800; color: #0d4a1e; text-align: center; line-height: 1.3; margin-top: 2px; }
</style>
</head>
<body>
  <div class="outer">
    <div class="inner">
      <div class="logo">&#127793;</div>
      <div class="org-name">AgriTech Pro</div>
      <div class="tagline">Smart Agriculture Platform &middot; Malawi</div>

      <div class="cert-text">This is to certify that</div>
      <div class="student-name">{{ $enrollment->user->full_name }}</div>
      <div class="cert-text">has successfully completed the course</div>
      <div class="course-title">{{ $enrollment->course->title }}</div>

      <div class="proficiency">
        demonstrating proficiency in {{ ucwords(str_replace('_', ' ', $enrollment->course->category)) }}
        and a commitment to modern farming excellence.
      </div>

      <div class="meta-row">
        <div class="meta-item">
          <div class="meta-val">{{ $enrollment->course->total_lessons }} Lessons</div>
          <div class="meta-lbl">Completed</div>
        </div>
        <div class="meta-item">
          <div class="meta-val">
            @if($enrollment->course->total_duration_minutes > 0)
              {{ intdiv($enrollment->course->total_duration_minutes, 60) }}h {{ $enrollment->course->total_duration_minutes % 60 }}m
            @else
              &mdash;
            @endif
          </div>
          <div class="meta-lbl">Course Duration</div>
        </div>
        <div class="meta-item">
          <div class="meta-val">{{ $enrollment->completed_at ? $enrollment->completed_at->format('M j, Y') : now()->format('M j, Y') }}</div>
          <div class="meta-lbl">Completion Date</div>
        </div>
        <div class="meta-item">
          <div class="meta-val">100%</div>
          <div class="meta-lbl">Final Score</div>
        </div>
      </div>

      <div class="signatures">
        <div class="sig-block">
          <div class="sig-name">AgriTech Pro</div>
          <div class="sig-title">Platform Director</div>
        </div>
        <div class="sig-block">
          <div class="sig-name">{{ $enrollment->course->instructor->name ?? 'Instructor' }}</div>
          <div class="sig-title">Course Instructor</div>
        </div>
      </div>

      <div class="cert-id">Certificate ID: {{ $enrollment->certificate_number }}</div>

      <div class="seal">
        <div class="seal-icon">&#127942;</div>
        <div class="seal-text">VERIFIED<br>CERTIFICATE</div>
      </div>
    </div>
  </div>
</body>
</html>
