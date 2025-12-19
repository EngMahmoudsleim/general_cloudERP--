<button id="notification-toggle">الإشعارات</button>
<ul class="notifications-list" id="notifications">
    @if(!empty($notifications_data))
      @foreach($notifications_data as $notification_data)
        <li class="@if(empty($notification_data['read_at'])) unread @endif notification-li tw-flex tw-items-center tw-gap-2 tw-px-3 tw-py-2 tw-text-sm tw-font-medium tw-text-gray-600 tw-transition-all tw-duration-200 tw-rounded-lg hover:tw-text-gray-900 hover:tw-bg-gray-100">
          <a href="{{$notification_data['link'] ?? '#'}}" @if(isset($notification_data['show_popup'])) class="show-notification-in-popup" @endif>
            <i class="notif-icon {{$notification_data['icon_class'] ?? ''}}"></i>
            <span class="notif-info">{!! $notification_data['msg'] ?? '' !!}</span>
            <span class="time">{{$notification_data['created_at']}}</span>
          </a>
        </li>
      @endforeach
    @else
      <li class="text-center no-notification notification-li">
        @lang('lang_v1.no_notifications_found')
      </li>
    @endif
</ul>

<style>
.notifications-list {
    transition: max-height 0.3s ease-in-out;
    max-height: 0;
    overflow: hidden;
}

.notifications-list.active {
    max-height: 300px; /* تغيير الارتفاع كما هو مطلوب */
}
</style>

<script>
document.getElementById('notification-toggle').addEventListener('click', function() {
    const notificationsList = document.getElementById('notifications');
    notificationsList.classList.toggle('active');
});
</script>