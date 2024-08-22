import React, { useState, useEffect } from 'react';
import axios from 'axios';

const TicTacToe = () => {
    const [board, setBoard] = useState([["", "", ""], ["", "", ""], ["", "", ""]]);
    const [currentTurn, setCurrentTurn] = useState('x');
    const [score, setScore] = useState({ x: 0, o: 0 });
    const [victory, setVictory] = useState(null);
    const [animateCells, setAnimateCells] = useState([[], [], []]);
    const [winningCombination, setWinningCombination] = useState([]);
    const [gameMode, setGameMode] = useState(null); // 'human' or 'ai'

    useEffect(() => {
        fetchGameState();
    }, []);

    const fetchGameState = async () => {
        try {
            const response = await axios.get('/api');
            setBoard(response.data.board);
            setCurrentTurn(response.data.currentTurn);
            setScore(response.data.score);
            setVictory(response.data.victory);
            setAnimateCells([[], [], []]);
            setWinningCombination([]);
        } catch (error) {
            console.error('Error fetching game state', error);
        }
    };

    const handleCellClick = async (x, y) => {
        if (board[x][y] === "" && !victory) {
            try {
                const response = await axios.post(`/api/${currentTurn}`, { x, y, opponent: gameMode === 'ai' ? 'ai' : 'human' });
                setBoard(response.data.board);
                setCurrentTurn(response.data.currentTurn);
                setScore(response.data.score);
                setVictory(response.data.victory);

                if (response.data.victory) {
                    // Identify and set the winning combination
                    const winningCombo = findWinningCombination(response.data.board, response.data.victory);
                    setWinningCombination(winningCombo);
                }

                triggerAnimation(x, y);
            } catch (error) {
                console.error('Error making move', error);
            }
        }
    };

    const triggerAnimation = (x, y) => {
        // Create a copy of the animateCells state
        const newAnimateCells = [...animateCells];
        // Set the animation state to true for the clicked cell
        newAnimateCells[x][y] = true;
        setAnimateCells(newAnimateCells);

        // Remove the animation class after the animation completes
        setTimeout(() => {
            const resetAnimateCells = [...newAnimateCells];
            resetAnimateCells[x][y] = false;
            setAnimateCells(resetAnimateCells);
        }, 1000); // Duration of the animation
    };


    const handleRestart = async () => {
        try {
            const response = await axios.post('/api/restart');
            setBoard(response.data.board);
            setCurrentTurn(response.data.currentTurn);
            setScore(response.data.score);  // Update the score after restart
            setVictory(null);
            setWinningCombination([]);
        } catch (error) {
            console.error('Error restarting game', error);
        }
    };

    const handleReset = async () => {
        try {
            await axios.delete('/api');
            fetchGameState();
        } catch (error) {
            console.error('Error resetting game', error);
        }
    };

    const findWinningCombination = (board, victory) => {
        const combinations = [
            // Horizontal
            [[0, 0], [0, 1], [0, 2]],
            [[1, 0], [1, 1], [1, 2]],
            [[2, 0], [2, 1], [2, 2]],
            // Vertical
            [[0, 0], [1, 0], [2, 0]],
            [[0, 1], [1, 1], [2, 1]],
            [[0, 2], [1, 2], [2, 2]],
            // Diagonal
            [[0, 0], [1, 1], [2, 2]],
            [[0, 2], [1, 1], [2, 0]],
        ];

        for (const combination of combinations) {
            const [a, b, c] = combination;
            if (board[a[0]][a[1]] === victory && board[b[0]][b[1]] === victory && board[c[0]][c[1]] === victory) {
                return combination;
            }
        }
        return [];
    };


    return (
        <div className="tic-tac-toe bg-white p-6 rounded-lg shadow-lg">
            {!gameMode ? (
                <div className="text-center">
                    <h1 className="text-4xl font-bold mb-8 text-center text-gray-800">Choose Game Mode</h1>
                    <button
                        onClick={() => setGameMode('human')}
                        className="bg-blue-500 text-white py-2 px-4 rounded-lg mr-4 hover:bg-blue-600 transition-transform transform hover:scale-105"
                    >
                        Player vs Player
                    </button>
                    <button
                        onClick={() => setGameMode('ai')}
                        className="bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition-transform transform hover:scale-105"
                    >
                        Player vs AI
                    </button>
                </div>
            ) : (
                <>
                    <h1 className="text-4xl font-bold mb-8 text-center text-gray-800">Tic-Tac-Toe</h1>
                    <div className="grid grid-col gap-4">
                        {board.map((row, rowIndex) => (
                            <div key={rowIndex} className="grid grid-cols-3 gap-4">
                                {row.map((cell, cellIndex) => (
                                    <button
                                        key={cellIndex}
                                        className={`w-20 h-20 m-auto text-6xl font-bold flex items-center justify-center border-2 rounded-lg ${cell === 'x' ? 'text-red-500' : cell === 'o' ? 'text-blue-500' : 'text-gray-700 hover:bg-gray-200'
                                            } ${animateCells[rowIndex][cellIndex] ? 'animate-jump' : ''
                                            } ${winningCombination.some(combo => combo[0] === rowIndex && combo[1] === cellIndex)
                                                ? 'animate-pulse bg-green-200'
                                                : ''
                                            }`}
                                        onClick={() => handleCellClick(rowIndex, cellIndex)}
                                    >
                                        {cell === 'x' ? 'X' : cell === 'o' ? 'O' : ''}
                                    </button>
                                ))}
                            </div>
                        ))}
                    </div>
                    <div className="mt-8 text-center">
                        <button
                            onClick={handleRestart}
                            className="bg-blue-500 text-white py-2 px-4 rounded-lg mr-4 hover:bg-blue-600 transition-transform transform hover:scale-105"
                        >
                            Restart
                        </button>
                        <button
                            onClick={handleReset}
                            className="bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition-transform transform hover:scale-105"
                        >
                            Reset Game
                        </button>
                    </div>
                    <div className="mt-8 text-center text-lg text-gray-800">
                        <p>Player X: <span className="font-bold">{score.x}</span></p>
                        <p>Player O: <span className="font-bold">{score.o}</span></p>
                    </div>
                </>
            )}
        </div>

    );
};

export default TicTacToe;
