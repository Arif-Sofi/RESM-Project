<!DOCTYPE html>
<html>
<head>
    <title>Rooms</title>
</head>
<body>
    <h1>Rooms</h1>
    <div>
        @foreach($rooms as $room)
            <div>
                <h3>{{ $room->name }}</h3>
                <p>{{ $room->description }}</p>
            </div>
        @endforeach
    </div>
</body>
</html>
