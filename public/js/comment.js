document.addEventListener('DOMContentLoaded', function () {
    const commentForm = document.querySelector('.comment-add-wrapper');
    const commentTextArea = commentForm.querySelector('.comment');
    const commentList = document.querySelector('.comments');

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
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                
                const comment = document.createElement('div');
                
                comment.innerHTML = `
                    <form id="comment-${data.id}" action="${data.update_url}" class="comment-wrapper mt-4 border-none" method="post">
                    <input type="hidden" name="_method" value="put">
                    <input type="hidden" name="_token" value="${data.csrf_token}">
                    <p>
                        <span>${data.username}</span>
                        <span class="text-black-50 ps-3">${data.created_at}</span>
                        <a class="del-btn-1" href="javascript:;" onclick="submitDelForm(${data.id})">削除</a>
                    </p>

                    <textarea name="comment-${data.id}" readonly="" class="comment mb-2 form-control auto-resize-textarea" style="height: 60px;">${commentTextArea.value}</textarea>
                    <div class="text-end mb-3">
                        <button type="submit" style="display:none;" class="comment-save btn btn-primary px-3">保存</button>
                        <button class="comment-edit btn btn-secondary px-3">編集</button>
                    </div>
                    </form>
                    <form class="del-form-${data.id}" action="${data.delete_url}" method="post">
                        <input type="hidden" name="_method" value="delete">
                        <input type="hidden" name="_token" value="${data.csrf_token}">
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

    // コメント一覧を取得して表示する関数
    async function fetchAndDisplayComments(ticketId) {
        
        try {
            const response = await fetch(`/comment/${ticketId}`);
            
            if (!response.ok) {
                throw new Error('コメント一覧の取得に失敗しました');
            }
            
            const data = await response.json();
            const comments = data.comments;
            const commentsContainer = document.querySelector('._comments');
            
            // reset
            commentsContainer.innerHTML = '';

            comments.forEach((c) => {
                
                const comment = document.createElement('div');

                comment.innerHTML = `
                    <form id="comment-${c.id}" action="" class="comment-wrapper mt-4" method="post">
                    <input type="hidden" name="_method" value="put">
                    <input type="hidden" name="_token" value="${data.csrf_token}">
                    <p>
                        <span>${c.user.name}</span>
                        <span class="text-black-50 ps-3">${c.created_at}</span>
                        <a class="del-btn-1" href="javascript:;" onclick="submitDelForm(${c.id})">削除</a>
                    </p>

                    <textarea name="comment-${c.id}" readonly="" class="comment mb-2 form-control auto-resize-textarea" style="height: 60px;">${c.comment}</textarea>
                    <div class="text-end mb-3">
                        <button type="submit" style="display:none;" class="comment-save btn btn-primary px-3">保存</button>
                        <button class="comment-edit btn btn-secondary px-3">編集</button>
                    </div>
                    </form>
                    <form class="del-form-${c.id}" action="" method="post">
                        <input type="hidden" name="_method" value="delete">
                        <input type="hidden" name="_token" value="${data.csrf_token}">
                    </form>
                    `;
                commentsContainer.appendChild(comment);
            });
        } catch (error) {
            console.error(error);
        }
    }

    const currentUrl = window.location.href;
    const urlParts = currentUrl.split('/');
    const ticketId = urlParts[urlParts.length - 1];
    fetchAndDisplayComments(ticketId);

});
