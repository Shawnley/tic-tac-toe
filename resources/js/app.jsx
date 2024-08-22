import React from 'react';
import ReactDOM from 'react-dom/client';
import TicTacToe from './components/TicTacToe';


function App() {
    return (<div className="container mx-auto p-4"><TicTacToe /></div>);
}

ReactDOM.createRoot(document.getElementById('app')).render(<App />);
