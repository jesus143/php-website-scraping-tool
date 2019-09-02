@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">City Name</th>
                                <th scope="col">Name Of Business</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Yelp Listing Url</th>
                                <th scope="col">Website</th>
                                <th scope="col">Keyword</th>
                                <th scope="col">Ago</th>
                             </tr>
                        </thead>
                        <tbody>
                        @foreach($records as $record)
                            <tr>
                                <td>
                                    {{ $record->id }}
                                </td>
                                <td>
                                    {{ $record->city_name }}
                                </td>
                                <td>
                                    {{ $record->name_of_business }}
                                </td>
                                <td>
                                    {{ $record->phone }}
                                </td>
                                <td>
                                    {{ $record->yelp_listing_url }}
                                </td>
                                <td>
                                    {{ $record->website }}
                                </td>
                                <td>
                                    {{ $record->keyword }}
                                </td>
                                <td>
                                    {{ $record->created_at->ago() }}
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $records->links() }}

    </div>
</div>
@endsection
