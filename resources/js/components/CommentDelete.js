// コメント削除
function submitDelForm(commentId, onCommentAction) {
    if (!window.confirm('コメントを削除します。よろしいですか？')) {
        return false;
    }
    
    const form = document.querySelector('.del-form-' + commentId);
    fetch(form.action, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
        },
    }).then(response => {
        if (response.ok) {
            onCommentAction();
        }
    });
}

export default submitDelForm;