document.addEventListener('DOMContentLoaded', function () {
    const commentForm = document.querySelector('.comment-add-wrapper');
    const commentTextArea = commentForm.querySelector('.comment');
    const commentList = document.querySelector('.comments');
    
    const currentUrl = window.location.href;
    const urlParts = currentUrl.split('/');
    const ticketId = urlParts[urlParts.length - 1];
    fetchAndDisplayComments(ticketId);

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
                commentTextArea.value = '';
                fetchAndDisplayComments(ticketId);
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
        const response = await fetch(`/comment/${ticketId}`);
        
        if (!response.ok) {
            throw new Error('コメント一覧の取得に失敗しました');
        }
        
        const data = await response.json();
        const comments = data.comments;
        const commentsContainer = document.querySelector('.comments');
        
        // reset
        commentsContainer.innerHTML = '';
        const loginId = document.getElementById('user-id').getAttribute('data-user-id');
        
        comments.forEach((c) => {
            
            const comment = document.createElement('div');
            
            del_link = '';
            edit_link = '';
            
            if (c.user.id == loginId) {
                del_link = `<a class="del-btn-1" href="javascript:;" onclick="submitDelForm(${c.id})">削除</a>`;
                edit_link = `<div class="text-end mb-3">
                            <button type="submit" style="display:none;" class="comment-save btn btn-primary px-3">保存</button>
                            <button class="comment-edit btn btn-secondary px-3">編集</button>
                            </div>`
            } 

            comment.innerHTML = `
                <form data-comment="${c.id}" action="/comment/update/${c.id}" class="comment-wrapper mt-4" method="post">
                <input type="hidden" name="_method" value="put">
                <input type="hidden" name="_token" value="${data.csrf_token}">
                <p>
                    <span>${c.user.name}</span>
                    <span class="text-black-50 ps-3">${c.created_at}</span>
                    ${del_link}
                </p>

                <textarea name="comment" readonly="" class="comment mb-2 form-control auto-resize-textarea" style="height: 60px;">${c.comment}</textarea>
                ${edit_link}
                </form>
                <form class="comment-del del-form-${c.id}" action="/comment/delete/${c.id}" method="post">
                    <input type="hidden" name="_method" value="delete">
                    <input type="hidden" name="_token" value="${data.csrf_token}">
                </form>
                `;
            commentsContainer.appendChild(comment);
        });

        // 最後のコメントの下枠線を消す
        const lastComment = commentsContainer.lastElementChild;
        const lastCommentForm = lastComment.querySelector('form');
        lastCommentForm.classList.add('border-none');
    }

});
