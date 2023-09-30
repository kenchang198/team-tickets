<x-layout>

<div class="container-fluid">
    <nav class="my-4" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">TOP</a></li>
            <li class="breadcrumb-item"><a href="{{ route('project.detail', $project) }}">{{ $project->project_name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $ticket->ticket_name }}</li>
        </ol>
    </nav>
    <div class="table-wrap table-responsive pt-3">

        <h1 class="mb-3">{{ $ticket->ticket_name }}</h1>
        @if (!$ticket->responsiblePerson->del_flg)
        <p class="mb-3"><b>担当: {{ $ticket->responsiblePerson->name }}</b></p>
        @else
        <p class="mb-3 text-decoration-line-through"><b>担当: {{ $ticket->responsiblePerson->name }}</b></p>
        @endif
        <div class="mb-5 created-updated">
            <ul>
                <li>開始日: {{ $start_date_f }}</li>
                <li class="pt-1">期日　: {{ $end_date_f }}</li>
            </ul>
        </div>
        <form action="{{ route('ticket.status', [$project, $ticket]) }}" class="t-status mb-4" method="post">
            @csrf
            @method('put')
            <div class="d-flex justify-content-end">
                <select name="t-status" id="t-status" class="me-2 form-control">
                    @foreach($statuses as $code => $name)
                        <option @if ($ticket->status_code == $code) selected @endif value="{{$code}}">{{$name}}</option>
                    @endforeach
                </select>
                <div>
                <button class="btn btn-primary px-3" type="submit">更新</button>
                </div>
            </div>
        </form>
        <div class="t-content ps-3 mb-3">
            <pre>{{ $ticket->content }}</pre>
        </div>
        @if ($ticket->hasUpdatePolicy())
        <div class="text-end">
            <a href="{{ route('ticket.edit', [$project, $ticket]) }}">編集</a>
        </div>
        @endif
        <div class="ps-3 text-black-50">
            <p>作成日: {{ $created_at }}　{{ $ticket->createUser->name }}</p>
            <p >更新日: {{ $updated_at }}　{{ $ticket->updateUser->name }}</p>
        </div>
    </div>

    <div class="mt-5 comments"></div>

    <form class="comment-add-wrapper" action="{{ route('comment.store', [$project, $ticket]) }}" method="post">
        @csrf
        @error('comment')
        <div class="alert alert-danger">
            <ul>
                <li class="_error-msg">{{ $message }}</li>
            </ul>
        </div>
        @enderror
        <p>コメントを追加</p>
        <textarea class="comment mb-2 form-control auto-resize-textarea" name="comment" id="" cols="20" rows="3">{{ old('comment') }}</textarea>
        <div class="mb-3 text-end">
            <button class="btn btn-primary px-3" type="submit">追加</button>
        </div>
    </form>
    <div class="mb-5">
            <p>要確認 : </p>
            <ul>
                @foreach($ticket->users as $user)
                <li>{{ $user->name }}</li>
                @endforeach
            </ul>
    </div>
    @if ($ticket->hasDeletePolicy())
    <div class="t-del-btn d-flex justify-content-end mb-5">
        <form class="ps-2" action="{{ route('ticket.delete', [$project, $ticket]) }}" method="post">
            @csrf
            @method('delete')
            <button type="submit" class="btn btn-danger px-3">チケットを削除する</button>
        </form>
    </div>
    @endif
    <div class="mt-5 mb-5">
        <a class="btn btn-secondary px-3" href="{{ route('project.detail', $project) }}">戻る</a>
    </div>
</div>
<div id="user-id" data-user-id="{{ Auth::user()->id }}"></div>
<script src="/js/comment.js"></script>
<script>
    function autoResizeTextarea(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }

    // 投稿されたコメントの文字数に合わせてテキストエリアの高さを調整する
    window.addEventListener('load', function() {
        const autoResizeTextareas = document.querySelectorAll('.auto-resize-textarea');
        autoResizeTextareas.forEach(textarea => autoResizeTextarea(textarea));
    });

    // 新たに追加するコメントの文字数に合わせてテキストエリアの高さを調整する
    const autoResizeTextareas = document.querySelectorAll('.auto-resize-textarea');
    
    autoResizeTextareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
        autoResizeTextarea(textarea);
        });
    });
</script>
</x-layout>