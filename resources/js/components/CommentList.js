// コメント一覧のコンポーネント
import React from 'react';

function CommentList({comments, csrfToken ,loginId}) {

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
