import React from 'react';
import ReactDOM from 'react-dom';

import { useState } from 'react';
import { useEffect } from 'react';

import CommentList from './CommentList';
import CommentForm from './CommentForm';

const currentUrl = window.location.href;
const urlParts = currentUrl.split('/');
const ticketId = urlParts[urlParts.length - 1];

const loginId = document.getElementById('user-id').getAttribute('data-user-id');

// コメント一覧を取得する関数をエクスポート
export async function fetchComments(ticketId, setComments, setCsrfToken) {

    const response = await fetch(`/comment/${ticketId}`);
    const data = await response.json();
    
    setComments(data.comments);
    setCsrfToken(data.csrf_token);
}

function App() {
    const [comments, setComments] = useState([]);
    const [csrfToken, setCsrfToken] = useState(''); // CSRFトークンの状態を保持
    
    useEffect(() => {
        fetchComments(ticketId, setComments, setCsrfToken);
    }, [ticketId]);

    // // コメント投稿成功時にコメント一覧を再取得するコールバック
    const handleCommentPosted = () => {
        fetchComments(ticketId, setComments, csrfToken);
    };

    return (
        <>
            <CommentList comments={comments} csrfToken={csrfToken} loginId={loginId}/>
            <CommentForm csrfToken={csrfToken} ticketId={ticketId} onCommentPosted={handleCommentPosted} />
        </>
    );
}

export default App;

if (document.getElementById('app')) {
    ReactDOM.render(<App />, document.getElementById('app'));
}