<x-mail::message>
    <strong> # Ingredients Level Alert </strong>

    The following ingredients are running low:

    <x-mail::table>
        | Ingredient | Stock | Level | Percentage |
        |:-------------:|:-------------:|:--------:|:-------------:|
        @foreach ($ingredients as $ingredient)
            | {{ $ingredient->name }} | {{ $ingredient->stock }} | {{ $ingredient->level }} |
            {{ number_format(($ingredient->level / $ingredient->stock) * 100, 2) }}% |
        @endforeach
    </x-mail::table>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
