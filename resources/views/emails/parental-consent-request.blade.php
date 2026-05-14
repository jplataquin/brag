<x-mail::message>
# Parental Consent Request

Hello {{ $user->parent_firstname }} {{ $user->parent_lastname }},

Your child, **{{ $user->username }}**, has registered for an account on **Brag Arena**.

Because they are under 18, we require your consent to complete their account creation.

<x-mail::panel>
**Child Details:**
- Username: {{ $user->username }}
- Email: {{ $user->email }}
</x-mail::panel>

Please review their application and choose one of the options below:

<x-mail::button :url="route('parental.confirm', ['token' => $user->parent_consent_token])" color="success">
Approve Account
</x-mail::button>

<x-mail::button :url="route('parental.reject', ['token' => $user->parent_consent_token])" color="error">
Reject & Delete Account
</x-mail::button>

If you did not expect this email, you can safely ignore it or click Reject to ensure the account is removed.

Thanks,<br>
The Brag Arena Team
</x-mail::message>
