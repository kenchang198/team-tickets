document.addEventListener('DOMContentLoaded', function () {
    const commentForm = document.querySelector('.comment-add-wrapper');
    const commentTextArea = commentForm.querySelector('.comment');
    const commentList = document.querySelector('.comments'); // コメント一覧を表示する要素

    commentForm.addEventListener('submit', function (e) {
        e.preventDefault();
        
        const formData = new FormData(commentForm);

        fetch(commentForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': formData.get('_token'),
            },
        })
        .then(response => response.json()) // サーバーからのJSONレスポンスを解析
        .then(data => {
            if (data.success) {
                // コメントが成功裏に投稿された場合の処理
                
                const comment = document.createElement('div');
                
                comment.innerHTML = `
                    <form id="comment-1" action="http://localhost:50080/comment/update/1" class="comment-wrapper mt-4 border-none" method="post">
                    <input type="hidden" name="_method" value="put">
                    <input type="hidden" name="_token" value="1fh97zbF7vBWHVRuEiDYAXkgLkBIL2ced4DbDEQ7">
                    <p>
                        <span>投稿者の名前</span>
                        <span class="text-black-50 ps-3">2023/mm/dd HH:MM:SS</span>
                        <a class="del-btn-1" href="javascript:;" onclick="submitDelForm(1)">削除</a>
                    </p>

                    <textarea name="comment-1" readonly="" class="comment mb-2 form-control auto-resize-textarea" style="height: 60px;">${commentTextArea.value}</textarea>
                    <div class="text-end mb-3">
                        <button type="submit" style="display:none;" class="comment-save btn btn-primary px-3">保存</button>
                        <button class="comment-edit btn btn-secondary px-3">編集</button>
                    </div>
                    </form>
                    `;

                commentList.append(comment);
                
                // 挿入したコメントの直前のコメントの下枠を削除
                comment.previousElementSibling.querySelector('form').classList.remove('border-none');
                
                commentTextArea.value = ''; // テキストエリアをクリア
            } else {
                alert('コメントの投稿に失敗しました');
            }
        })
        .catch(error => {
            console.error('エラーが発生しました:', error);
        });
    });
});
