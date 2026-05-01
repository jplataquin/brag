<x-mail::message>
# New Feedback from the Arena

**From:** {{ $user->username }} ({{ $user->email }})  
**User ID:** {{ $user->id }}  
**Subject:** {{ $subjectLine }}

**Message:**
{{ $feedbackMessage }}

<x-mail::button :url="config('app.url') . '/user/' . $user->username">
View User Profile
</x-mail::button>

Thanks,<br>
{{ config('app.name') }} System
</x-mail::message>
