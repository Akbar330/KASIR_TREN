@extends('layouts.app')

@section('title', 'Kalender Booking')

@push('styles')
<style data-page-style>
    #bookingCalendar{min-height:650px}.fc .fc-toolbar-title{font-size:1.25rem;font-weight:750;color:#171718}.fc .fc-button-primary{background:#4a6de0;border-color:#4a6de0;border-radius:8px;text-transform:capitalize}.fc .fc-button-primary:hover,.fc .fc-button-primary:not(:disabled).fc-button-active{background:#3655c0;border-color:#3655c0}.fc .fc-daygrid-day-number,.fc .fc-col-header-cell-cushion{color:#52545e;text-decoration:none}.fc .fc-event{border:0;border-radius:6px;padding:2px 4px;font-weight:600}.fc .fc-day-today{background:#f4f5f7!important}
    @media(max-width:767px){#bookingCalendar{min-height:520px}.fc .fc-toolbar{gap:.5rem;flex-wrap:wrap}.fc .fc-toolbar-title{font-size:1rem}.fc .fc-button{font-size:.8rem;padding:.35rem .5rem}}
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-end gap-3 flex-wrap mb-4"><div><p class="text-success fw-bold small text-uppercase mb-1">Perencanaan lapangan</p><h2 class="fw-bold mb-1" style="letter-spacing:-.04em">Kalender booking</h2><p class="text-muted mb-0">Lihat jadwal dan perpindahan booking dalam tampilan kalender.</p></div><a href="{{ route('bookings.index') }}" class="btn btn-outline-success"><i class="fas fa-table-list me-2"></i>Tampilan jadwal</a></div>
<div class="card"><div class="card-header d-flex justify-content-between align-items-center"><div><strong>Agenda lapangan</strong><div class="text-muted small fw-normal mt-1">Klik sebuah jadwal untuk melihat detailnya.</div></div><span class="badge bg-success"><i class="fas fa-circle me-1" style="font-size:.5rem"></i>Booking aktif</span></div><div class="card-body"><div id="bookingCalendar"></div></div></div>
<button type="button" id="openBookingDetails" class="d-none" data-bs-toggle="modal" data-bs-target="#bookingDetailsModal"></button>
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" aria-labelledby="bookingDetailsTitle" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><div><small class="text-success fw-bold text-uppercase">Booking lapangan</small><h5 class="modal-title fw-bold mt-1" id="bookingDetailsTitle"></h5></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="d-flex gap-3 align-items-start"><span class="rounded-3 p-3" style="background:#edf0ff;color:#4a6de0"><i class="far fa-clock"></i></span><div><div class="text-muted small">Waktu booking</div><strong id="bookingDetailsTime"></strong></div></div></div><div class="modal-footer"><button type="button" class="btn btn-light border" data-bs-dismiss="modal">Tutup</button></div></div></div></div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales-all.global.min.js"></script>
<script>
function renderBookingCalendar(){const calendarElement=document.getElementById('bookingCalendar');if(!calendarElement||!window.FullCalendar)return;const calendar=new FullCalendar.Calendar(calendarElement,{initialView:'dayGridMonth',locale:'id',height:'auto',expandRows:true,nowIndicator:true,headerToolbar:{left:'prev,next today',center:'title',right:'dayGridMonth,timeGridWeek,timeGridDay,listWeek'},buttonText:{today:'Hari ini',month:'Bulan',week:'Minggu',day:'Hari',list:'Agenda'},allDaySlot:false,slotMinTime:'06:00:00',slotMaxTime:'24:00:00',events:@json($bookings),eventClick:function(info){const begin=info.event.start?info.event.start.toLocaleString('id-ID',{dateStyle:'full',timeStyle:'short'}):'';const end=info.event.end?info.event.end.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'}):'';document.getElementById('bookingDetailsTitle').textContent=info.event.title;document.getElementById('bookingDetailsTime').textContent=begin+(end?' - '+end:'');document.getElementById('openBookingDetails').click()}});calendar.render()}
renderBookingCalendar();
</script>
@endpush
