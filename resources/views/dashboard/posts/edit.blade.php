@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Posts</h1>
</div>


<div class="col-lg-8">
    <form method="post" action="/dashboard/posts/{{ $post->slug }}" class="mb-5">
        @method('put')
        @csrf
        <div class="mb-3 row">
            <div class="col-md-6">
                <label for="date" class="form-label">Date</label>
                <div class="input-group date" id="datepicker">
                    <input type="text" class="form-control @error('date') is-invalid @enderror" placeholder="dd-mm-yyyy" id="date" name='date' readonly required autofocus value="{{ old('date', $post->date) }}">
                    @error('date')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                    <span class="input-group-append">
                        <!-- You can add any additional elements here if needed -->
                    </span>
                </div>
            </div>
            <div class="col-md-6">
                <label for="category" class="form-label">Province</label>
                <select class="form-select" name="category_id">
                    @foreach ( $categories as $category )
                        @if(old('category_id', $post->category_id) == $category->id)
                            <option value="{{ $category->id }}" selected>{{ $category->name }}</option>
                        @else
                            <option value="{{ $category->id }}" {{ $post ? 'disabled' : '' }}>{{ $category->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name='title' required autofocus value="{{ old('title', $post->title) }}">
            @error('title')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="temperature" class="form-label">Temperature</label>
            <input type="text" class="form-control @error('temperature') is-invalid @enderror" id="temperature" name='temperature' required autofocus value="{{ old('temperature', $post->temperature) }}">
            @error('temperature')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="rainfall" class="form-label">Rainfall</label>
            <input type="text" class="form-control @error('rainfall') is-invalid @enderror" id="rainfall" name='rainfall' required autofocus value="{{ old('rainfall', $post->rainfall) }}">
            @error('rainfall')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="humidity" class="form-label">Humidity</label>
            <input type="text" class="form-control @error('humidity') is-invalid @enderror" id="humidity" name='humidity' required autofocus value="{{ old('humidity', $post->humidity) }}">
            @error('humidity')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="windspeed" class="form-label">Windspeed</label>
            <input type="text" class="form-control @error('windspeed') is-invalid @enderror" id="windspeed" name='windspeed' required autofocus value="{{ old('windspeed', $post->windspeed) }}">
            @error('windspeed')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name='slug' required value="{{ old('slug', $post->slug) }}">
            @error('slug')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        {{-- <div class="mb-3">
            <label for="body" class="form-label">Body</label>
                @error('body')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
                <input id="body" type="hidden" name="body" value="{{ old('body', $post->body) }}">
                <trix-editor input="body"></trix-editor>
        </div> --}}
        <button type="submit" class="btn btn-primary">Update Post</button>
    </form>
</div>

<script>
    const title = document.querySelector("#title");
    const slug = document.querySelector("#slug");

    title.addEventListener("keyup", function() {
        let preslug = title.value;
        preslug = preslug.replace(/ /g,"-");
        slug.value = preslug.toLowerCase();
    });

    document.addEventListener('trix-file-accept', function(e){
        e.preventDefault()
    })
</script>


@endsection