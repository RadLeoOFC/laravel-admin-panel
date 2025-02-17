<!DOCTYPE html>
<html>
<head>
    <title>Membership Confirmation</title>
</head>
<body>
    <h1>Hello, {{ $membership->user->name }}!</h1>
    <p>Your membership has been successfully created.</p>
    <p><strong>Start Date:</strong> {{ $membership->start_date }}</p>
    <p><strong>End Date:</strong> {{ $membership->end_date }}</p>
    <p>Thank you for joining us!</p>
</body>
</html>
