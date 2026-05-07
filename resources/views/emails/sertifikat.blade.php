@component('mail::message')
# Sertifikat Magang PUSDATIN PUPR

Kepada Yth.  
**{{ $peserta->nama }}**

Dengan hormat,

Terlampir adalah **Sertifikat Magang** Anda dari Pusat Data dan Teknologi Informasi, Sekretariat Jenderal, Kementerian Pekerjaan Umum.

@if($pesan_tambahan)
---
{{ $pesan_tambahan }}
@endif

Terima kasih atas dedikasi dan kontribusi Anda selama menjalani program magang. Semoga pengalaman ini bermanfaat bagi pengembangan karir Anda ke depan.

Salam hormat,  
**Pusat Data dan Teknologi Informasi (PUSDATIN)**  
Kementerian Pekerjaan Umum

@endcomponent
