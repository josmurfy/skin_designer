// Original: shopmanager/sound.js
// ============================================
// GLOBAL AUDIO FUNCTIONS - v1.1
// Centralized sound functions for all ShopManager modules
// ============================================

function playErrorSound() {
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const oscillator2 = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    oscillator2.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    oscillator.frequency.value = 800;
    oscillator2.frequency.value = 400;
    oscillator.type = 'square';
    oscillator2.type = 'square';
    
    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
    oscillator.start(audioContext.currentTime);
    oscillator2.start(audioContext.currentTime);
    
    gainNode.gain.setValueAtTime(0, audioContext.currentTime + 0.1);
    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime + 0.15);
    gainNode.gain.setValueAtTime(0, audioContext.currentTime + 0.25);
    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime + 0.3);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
    
    oscillator.stop(audioContext.currentTime + 0.5);
    oscillator2.stop(audioContext.currentTime + 0.5);
}

function playWarningSound() {
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    oscillator.frequency.value = 700;
    oscillator.type = 'triangle';
    
    gainNode.gain.setValueAtTime(0.25, audioContext.currentTime);
    oscillator.start(audioContext.currentTime);
    
    // Pulsing warning sound
    gainNode.gain.setValueAtTime(0.25, audioContext.currentTime);
    gainNode.gain.setValueAtTime(0.05, audioContext.currentTime + 0.2);
    gainNode.gain.setValueAtTime(0.25, audioContext.currentTime + 0.4);
    gainNode.gain.setValueAtTime(0.05, audioContext.currentTime + 0.6);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.7);
    
    oscillator.stop(audioContext.currentTime + 0.7);
}

function playSuccessSound() {
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const oscillator2 = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    oscillator2.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    // Two-tone ascending sound
    oscillator.frequency.value = 600;
    oscillator2.frequency.value = 800;
    oscillator.type = 'sine';
    oscillator2.type = 'sine';
    
    // First tone
    gainNode.gain.setValueAtTime(0.4, audioContext.currentTime);
    oscillator.start(audioContext.currentTime);
    oscillator2.start(audioContext.currentTime + 0.15);
    
    // Fade out
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
    
    oscillator.stop(audioContext.currentTime + 0.5);
    oscillator2.stop(audioContext.currentTime + 0.5);
}
