import { initializeApp } from "https://www.gstatic.com/firebasejs/9.22.0/firebase-app.js";
import { getAnalytics } from "https://www.gstatic.com/firebasejs/9.22.0/firebase-analytics.js";
import { getAuth } from "https://www.gstatic.com/firebasejs/9.22.0/firebase-auth.js";

const firebaseConfig = {
  apiKey: "AIzaSyBXPK3sawYVWoItJy82towBSkEHG8-R77A",
  authDomain: "warehouse-ae667.firebaseapp.com",
  projectId: "warehouse-ae667",
  storageBucket: "warehouse-ae667.firebasestorage.app",
  messagingSenderId: "693416789938",
  appId: "1:693416789938:web:e435c1f87aad12ede2c6ab",
  measurementId: "G-QN3HZ1JXDD"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);
const auth = getAuth(app);

export { app, auth };
