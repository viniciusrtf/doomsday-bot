<h3>As promised, I'm here to bring you some existential threats.</h3>
<p>These are the asteroids that are dangerously approaching Earth in the next {{$user->neo_notification_days_in_advance}} day(s)</p>
<br>
<hr>
@foreach ($hazardousNeos as $neo)
    <strong>Name: </strong> {{$neo->name}}<br>
    <strong>Approach date: </strong> {{$neo->approach_date}}<br>
    <strong>Diameter (estimated): </strong> {{number_format($neo->estimated_diameter, 2, ',', '.')}} Km<br>
    <strong>Relative velocity: </strong> {{number_format($neo->relative_velocity, 2, ',', '.')}} Km/h<br>
    <strong>Distance: </strong> {{number_format($neo->mass_distance, 2, ',', '.')}} Km<br>
    <strong>More info: </strong> {{$neo->info_url}}<br>
    <hr>
@endforeach
<br>
<p>You'll not miss me. There're plenty of those rocky and icy stuck passing by every week.</p>