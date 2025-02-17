<!DOCTYPE html>
<html>
<head>
    <title>Membership Expiration Reminder</title>
</head>
<body>
    <h1>Hello, {{ $membership->user->name }}!</h1>
    <p>We wanted to remind you that your membership will expire soon.</p>
    <p><strong>End Date:</strong> {{ $membership->end_date }}</p>
    <p>Please renew your membership to continue enjoying our services.</p>
    <p>Thank you!</p>
</body>
</html>
