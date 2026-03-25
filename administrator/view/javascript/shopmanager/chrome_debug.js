// chrome_debug.js

// Fonction utilitaire pour consigner les messages dans la console
function logDebugMessage(type, message) {
    const timestamp = new Date().toISOString();
}

// Écouteur pour surveiller les messages entrants dans le background script
if (chrome.runtime && chrome.runtime.onMessage) {
    chrome.runtime.onMessage.addListener((request, sender, sendResponse) => {
        logDebugMessage('message received', {
            request,
            sender
        });

        // Réponse de test
        sendResponse({ status: 'success', data: 'Message received successfully.' });

        // Si une réponse asynchrone est nécessaire
        return true;
    });
}

// Surveillance des erreurs de runtime
if (chrome.runtime && chrome.runtime.lastError) {
    logDebugMessage('runtime error', chrome.runtime.lastError.message);
}

// Envoyer un message depuis un content script ou une popup
function sendMessageToBackground(data, callback) {
    chrome.runtime.sendMessage(data, (response) => {
        if (chrome.runtime.lastError) {
            logDebugMessage('sendMessage error', chrome.runtime.lastError.message);
        } else {
            logDebugMessage('response received', response);
            if (callback) callback(response);
        }
    });
}

// Exemple d'utilisation dans un content script ou popup
function testMessage() {
    sendMessageToBackground({ type: 'test', payload: 'Hello from content script' }, (response) => {
    });
}

// Surveillance des onglets
if (chrome.tabs) {
    chrome.tabs.onCreated.addListener((tab) => {
        logDebugMessage('tab created', tab);
    });

    chrome.tabs.onUpdated.addListener((tabId, changeInfo, tab) => {
        logDebugMessage('tab updated', { tabId, changeInfo, tab });
    });

    chrome.tabs.onRemoved.addListener((tabId, removeInfo) => {
        logDebugMessage('tab removed', { tabId, removeInfo });
    });
}

// Surveillance des événements de l'extension
if (chrome.runtime && chrome.runtime.onInstalled) {
    chrome.runtime.onInstalled.addListener((details) => {
        logDebugMessage('extension installed', details);
    });
}

if (chrome.runtime && chrome.runtime.onStartup) {
    chrome.runtime.onStartup.addListener(() => {
        logDebugMessage('extension startup', 'Extension has started.');
    });
}

// Ajouter les fonctions globalement
window.chromeDebug = {
    logDebugMessage,
    sendMessageToBackground,
    testMessage
};

chromeDebug.testMessage();
