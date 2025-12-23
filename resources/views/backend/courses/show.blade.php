@extends('backend.layouts.app')
@section('title', __('labels.backend.courses.title').' | '.app_name())

@push('after-styles')
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/amigo-sorter/css/theme-default.css')}}">
    <style>
        ul.sorter > span {
            display: inline-block;
            width: 100%;
            height: 100%;
            background: #f5f5f5;
            color: #333333;
            border: 1px solid #cccccc;
            border-radius: 6px;
            padding: 0px;
        }

        ul.sorter li > span .title {
            padding-left: 15px;
            width: 70%;
        }

        ul.sorter li > span .btn {
            width: 20%;
        }

        @media screen and (max-width: 768px) {

            ul.sorter li > span .btn {
                width: 30%;
            }

            ul.sorter li > span .title {
                padding-left: 15px;
                width: 70%;
                float: left;
                margin: 0 !important;
            }

        }


    </style>
@endpush

@section('content')

<div class="pb-3 d-flex justify-content-between">
    <h4>
        @lang('labels.backend.courses.title')
    </h4>
  
</div>
    <div class="card">

        <div class="card-body">
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered table-striped">

                {{-- Teachers --}}
                <tr>
                    <th>@lang('labels.backend.courses.fields.teachers')</th>
                    <td>
                        @forelse ($course->teachers ?? [] as $singleTeacher)
                            <span class="badge badge-info">{{ $singleTeacher->name }}</span>
                        @empty
                            <span class="text-muted">No Teachers</span>
                        @endforelse
                    </td>
                </tr>

                {{-- Title --}}
                <tr>
                    <th>@lang('labels.backend.courses.fields.title')</th>
                    <td>
                        @if($course->published)
                            <a target="_blank"
                               href="{{ route('courses.show', $course->slug) }}">
                                {{ $course->title }}
                            </a>
                        @else
                            {{ $course->title }}
                        @endif
                    </td>
                </tr>

                {{-- Slug --}}
                <tr>
                    <th>@lang('labels.backend.courses.fields.slug')</th>
                    <td>{{ $course->slug }}</td>
                </tr>

                {{-- Category (NULL SAFE) --}}
                <tr>
                    <th>@lang('labels.backend.courses.fields.category')</th>
                    <td>{{ optional($course->category)->name ?? '-' }}</td>
                </tr>

                {{-- Description --}}
                <tr>
                    <th>@lang('labels.backend.courses.fields.description')</th>
                    <td>{!! $course->description !!}</td>
                </tr>

                {{-- Course Image --}}
                <tr>
                    <th>@lang('labels.backend.courses.fields.course_image')</th>
                    <td>
                        @if(!empty($course->course_image))
                            <a href="{{ asset('storage/uploads/' . $course->course_image) }}" target="_blank">
                                <img src="{{ asset('storage/uploads/' . $course->course_image) }}" height="50">
                            </a>
                        @else
                            <span class="text-muted">No Image</span>
                        @endif
                    </td>
                </tr>

                {{-- Video --}}
                <tr>
                    <th>@lang('labels.backend.lessons.fields.media_video')</th>
                    <td>
                        @if(optional($course->mediaVideo)->url)
                            <a href="{{ $course->mediaVideo->url }}" target="_blank">
                                {{ $course->mediaVideo->url }}
                            </a>
                        @else
                            <span class="text-muted">No Videos</span>
                        @endif
                    </td>
                </tr>

                {{-- Dates --}}
                <tr>
                    <th>@lang('labels.backend.courses.fields.start_date')</th>
                    <td>{{ $course->start_date ?? '-' }}</td>
                </tr>

                <tr>
                    <th>@lang('labels.backend.courses.fields.expire_at')</th>
                    <td>{{ $course->expire_at ?? '-' }}</td>
                </tr>

                {{-- Published (Form::checkbox REMOVED) --}}
                <tr>
                    <th>@lang('labels.backend.courses.fields.published')</th>
                    <td>
                        <input type="checkbox" disabled {{ $course->published ? 'checked' : '' }}>
                    </td>
                </tr>

            </table>
        </div>
    </div>

    {{-- Timeline --}}
    @if(!empty($courseTimeline) && count($courseTimeline) > 0)
        <div class="row justify-content-center">
            <div class="col-lg-8 col-12">
                <h4>@lang('labels.backend.courses.course_timeline')</h4>
                <p class="mb-0">@lang('labels.backend.courses.listing_note')</p>
                <p>@lang('labels.backend.courses.timeline_description')</p>

                <ul class="sorter d-inline-block">
                    @foreach($courseTimeline as $item)
                        @if(optional($item->model)->published)
                            <li>
                                <span data-id="{{ $item->id }}" data-sequence="{{ $item->sequence }}">

                                    @if($item->model_type === 'App\Models\Test')
                                        <span class="btn btn-primary btn-sm">
                                            @lang('labels.backend.courses.test')
                                        </span>

                                    @elseif($item->model_type === 'App\Models\Lesson')
                                        <span class="btn {{ $item->model->live_lesson ? 'btn-info' : 'btn-success' }} btn-sm">
                                            {{ $item->model->live_lesson
                                                ? __('labels.backend.live_lessons.title')
                                                : __('labels.backend.courses.lesson') }}
                                        </span>
                                    @endif

                                    <span class="ml-2">{{ optional($item->model)->title }}</span>
                                </span>
                            </li>
                        @endif
                    @endforeach
                </ul>

                <a href="{{ route('admin.courses.index') }}"
                   class="btn btn-secondary float-left">
                    @lang('strings.backend.general.app_back_to_list')
                </a>

                <a href="#" id="save_timeline"
                   class="btn btn-primary float-right">
                    @lang('labels.backend.courses.save_timeline')
                </a>
            </div>
        </div>
    @endif
</div>

    </div>
@stop

@push('after-scripts')
    <script src="{{asset('plugins/amigo-sorter/js/amigo-sorter.min.js')}}"></script>
    <script>
        $(function () {
            $('ul.sorter').amigoSorter({
                li_helper: "li_helper",
                li_empty: "empty",
            });
            $(document).on('click', '#save_timeline', function (e) {
                e.preventDefault();
                var list = [];
                $('ul.sorter li').each(function (key, value) {
                    key++;
                    var val = $(value).find('span').data('id');
                    list.push({id: val, sequence: key});
                });

                $.ajax({
                    method: 'POST',
                    url: "{{route('admin.courses.saveSequence')}}",
                    data: {
                        _token: '{{csrf_token()}}',
                        list: list
                    }
                }).done(function () {
                    location.reload();
                });
            })
        });

    </script>
@endpush
