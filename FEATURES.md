# 🎯 Roadmap & Features

This document outlines the current state of the Habit Tracker API, including implemented features and future goals.

## ✅ Completed Features

### 🔐 Authentication & Identity
- **Email/Password:** Full registration, login, and secure password management.
- **Social Login:** Seamless integration with Apple and Google Sign-In.
- **Profile Management:** User settings and preference persistence.
- **Security:** Multi-device token management via Laravel Sanctum.

### 📅 Habit Core
- **CRUD Operations:** Comprehensive habit management with soft deletes.
- **Custom Frequencies:** Support for daily, weekly, or specific days of the week.
- **Tracking Logic:** Automatic calculation of total completions, current streaks, and records.

### 🎮 Gamification & Retention
- **XP System:** Logic-driven experience awarding with streak multipliers.
- **Leveling:** Exponential XP progression with hero level-ups.
- **Heroes System:** Template-based hero management, unlocking, and active hero switching.
- **Achievements:** 30+ automatic achievements with category-specific logic.
- **Smart Reminders:** Scheduled notifications (Push/Email) based on user timezone.

### 📊 Analytics & Reporting
- **Activity Stats:** Weekly and monthly completion rates.
- **Streak History:** Longest streaks and consistency metrics.
- **Hero Progress:** Visual breakdown of experience and level milestones.

---

## 🏗 In Progress

- **💳 Monobank Integration:** Automating habit completion based on transaction history (e.g., "Save Money" habit).
- **📱 Push Notification Service:** Fine-tuning Firebase/APNs integration for mobile delivery.
- **🧹 System Optimization:** Enhancing performance for high-frequency habit logs.

---

## 💡 Future Ideas

- **🏷 Categories & Tags:** Better organization for diverse habit sets (Health, Work, Personal).
- **🏁 Long-term Goals:** Grouping habits under larger objectives with specific deadlines.
- **📈 Advanced Analytics:** Heatmaps, trend charts, and CSV data export for power users.
- **🤝 Social Features:** Friend systems, global leaderboards, and shared challenges.
- **💎 Premium Tier:** Subscription-based features and exclusive epic heroes.
- **🌍 More Locales:** Expanding localization beyond English and Ukrainian.
