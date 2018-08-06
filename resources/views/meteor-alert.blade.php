<h3>
    Olá! Estes são os meteoros que oferecem algum perigo à Terra nos próximos {{ $user->doomsday_advance }} dia(s)
</h3>
<br>
<hr>
@foreach ($alerts as $alert)
    <strong>Nome: </strong> {{$alert->name}}<br>
    <strong>Data da aproximação: </strong> {{$alert->approach_date}}<br>
    <strong>Diâmetro estimado: </strong> {{number_format($alert->estimated_diameter, 2, ',', '.')}} Km<br>
    <strong>Velocidade relativa: </strong> {{number_format($alert->relative_velocity, 2, ',', '.')}} Km/h<br>
    <strong>Distância: </strong> {{number_format($alert->mass_distance, 2, ',', '.')}} Km<br>
    <strong>Mais detalhes: </strong> {{$alert->nasa_url}}<br>
    <hr>
@endforeach
