import React, { useState } from 'react';

function CommentForm({ csrfToken, ticketId, onCommentPosted }) {
    const [commentText, setCommentText] = useState('');
    const [error, setError] = useState('');

    const handleSubmit = async (e) => {
        e.preventDefault();

        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('comment', commentText);

        const response = await fetch(`/comment/${ticketId}`, {
            method: 'POST',
            body: formData,
        });

        const data = await response.json();

        if (data.success) {
            setCommentText('');
            onCommentPosted();
        } else {
            alert(data.errors.comment[0]);
        }
    };

    return (
        <form className="comment-add-wrapper" onSubmit={handleSubmit}>
            <p>コメントを追加</p>
            <textarea
                className="comment mb-2 form-control auto-resize-textarea"
                name="comment"
                cols="20"
                rows="3"
                value={commentText}
                onChange={(e) => setCommentText(e.target.value)}
            />
            <div className="mb-3 text-end">
                <button className="btn btn-primary px-3" type="submit">追加</button>
            </div>
            {error && <div className="text-danger">{error}</div>}
        </form>
    );
}

export default CommentForm;
