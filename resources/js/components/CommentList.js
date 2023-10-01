// コメント一覧のコンポーネント
import React from 'react';
import ReactDOM from 'react-dom';

import { useState } from 'react';
import { useEffect } from 'react';

const currentUrl = window.location.href;
const urlParts = currentUrl.split('/');
const ticketId = urlParts[urlParts.length - 1];

const loginId = document.getElementById('user-id').getAttribute('data-user-id');

function CommentList() {
    const [comments, setComments] = useState([]);
    const [csrfToken, setCsrfToken] = useState(''); // CSRFトークンの状態を保持

    useEffect(() => {
        // コメント一覧を取得する関数
        async function fetchComments() {
            const response = await fetch(`/comment/${ticketId}`);
            
            const data = await response.json();
            
            setComments(data.comments);
            
            // CSRFトークンを状態にセット
            setCsrfToken(data.csrf_token);
        }

        fetchComments();
    }, [ticketId]);

    return (
        <div className="mt-5 comments">
            {comments.map((comment) => (
                <div key={comment.id}>
                    <form
                        action={`/comment/update/${comment.id}`}
                        className="comment-wrapper mt-4"
                        method="post"
                    >
                        <input type="hidden" name="_method" value="put" />
                        <input type="hidden" name="_token" value={csrfToken} />
                        <p>
                            <span>{comment.user.name}</span>
                            <span className="text-black-50 ps-3">{comment.created_at}</span>
                            {comment.user.id == loginId && ( // if
                                <a className="del-btn-1 px-2" href="javascript:;" onClick={() => submitDelForm(comment.id)} >削除</a>
                            )}
                        </p>
                        <textarea
                            name="comment"
                            readOnly
                            className="comment mb-2 form-control auto-resize-textarea"
                            style={{ height: '60px' }}
                            value={comment.comment}
                        />
                        {comment.user.id == loginId && ( // if
                            <div className="text-end mb-3">
                                <button className="comment-edit-cancel btn btn-secondary px-3" style={{ display: 'none' }}>キャンセル</button>
                                <button type="submit" style={{ display: 'none' }} className="comment-save btn btn-primary px-3">保存</button>
                                <button className="comment-edit btn btn-secondary px-3">編集</button>
                            </div>
                        )}
                    </form>
                    <form
                        className={`comment-del del-form-${comment.id}`}
                        action={`/comment/delete/${comment.id}`}
                        method="post"
                    >
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="_token" value={csrfToken} />
                    </form>
                </div>
            ))}
        </div>
    );
}

export default CommentList;

if (document.getElementById('app')) {
    ReactDOM.render(<CommentList />, document.getElementById('app'));
}
