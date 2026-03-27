import express from "express";
import { createServer as createViteServer } from "vite";
import Database from "better-sqlite3";
import path from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const db = new Database("pes.db");

// Initialize Database
db.exec(`
  CREATE TABLE IF NOT EXISTS issuances (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    category TEXT NOT NULL,
    date TEXT NOT NULL,
    division TEXT NOT NULL,
    url TEXT
  );

  CREATE TABLE IF NOT EXISTS materials (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    type TEXT NOT NULL,
    date TEXT NOT NULL,
    division TEXT NOT NULL,
    url TEXT
  );

  CREATE TABLE IF NOT EXISTS divisions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT NOT NULL,
    head TEXT
  );

  CREATE TABLE IF NOT EXISTS dost_dx (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category TEXT NOT NULL, -- 'domain' or 'program'
    title TEXT NOT NULL,
    description TEXT NOT NULL
  );

  CREATE TABLE IF NOT EXISTS issuance_categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
  );

  CREATE TABLE IF NOT EXISTS subscriptions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
  );
`);

// Seed data if empty
const issuanceCount = db.prepare("SELECT count(*) as count FROM issuances").get() as { count: number };
if (issuanceCount.count === 0) {
  const insertIssuance = db.prepare("INSERT INTO issuances (title, category, date, division, url) VALUES (?, ?, ?, ?, ?)");
  insertIssuance.run("PES Memo 2024-001", "Memorandum", "2024-01-15", "Planning", "#");
  insertIssuance.run("Special Order No. 45", "Order", "2024-02-10", "Evaluation", "#");
}

const divisionCount = db.prepare("SELECT count(*) as count FROM divisions").get() as { count: number };
if (divisionCount.count === 0) {
  const insertDiv = db.prepare("INSERT INTO divisions (name, description, head) VALUES (?, ?, ?)");
  insertDiv.run("Planning Division", "Responsible for the formulation of the DOST Strategic Plan.", "Dir. Pedraza");
  insertDiv.run("Evaluation Division", "Conducts impact assessment and monitoring of DOST programs.", "Chief Evaluator");
  insertDiv.run("Project Management Division", "Oversees the implementation of special projects and inter-agency collaborations.", "Project Manager");
}

const materialCount = db.prepare("SELECT count(*) as count FROM materials").get() as { count: number };
if (materialCount.count === 0) {
  const insertMat = db.prepare("INSERT INTO materials (title, type, date, division, url) VALUES (?, ?, ?, ?, ?)");
  insertMat.run("DOST Strategic Plan 2023-2028", "PowerPoint", "2023-12-01", "Planning", "#");
  insertMat.run("Impact Assessment Infographic", "Infographic", "2024-01-20", "Evaluation", "#");
  insertMat.run("PES Functions Video", "Video", "2024-02-05", "Planning", "#");
}

const dxCount = db.prepare("SELECT count(*) as count FROM dost_dx").get() as { count: number };
if (dxCount.count === 0) {
  const insertDX = db.prepare("INSERT INTO dost_dx (category, title, description) VALUES (?, ?, ?)");
  insertDX.run("domain", "Digital Infrastructure", "Modernizing the backbone of DOST operations.");
  insertDX.run("domain", "Digital Governance", "Streamlining processes through policy and automation.");
  insertDX.run("domain", "Digital Services", "Delivering citizen-centric online platforms.");
}

const categoryCount = db.prepare("SELECT count(*) as count FROM issuance_categories").get() as { count: number };
if (categoryCount.count === 0) {
  const insertCat = db.prepare("INSERT INTO issuance_categories (name) VALUES (?)");
  ["Memorandum", "Order", "Letter", "Circular", "Notice"].forEach(cat => insertCat.run(cat));
}

async function startServer() {
  const app = express();
  const PORT = 3000;

  app.use(express.json());

  // API Routes
  app.get("/api/issuances", (req, res) => {
    const rows = db.prepare("SELECT * FROM issuances ORDER BY date DESC").all();
    res.json(rows);
  });

  app.post("/api/issuances", (req, res) => {
    const { title, category, date, division, url } = req.body;
    const info = db.prepare("INSERT INTO issuances (title, category, date, division, url) VALUES (?, ?, ?, ?, ?)").run(title, category, date, division, url);
    res.json({ id: info.lastInsertRowid });
  });

  app.delete("/api/issuances/:id", (req, res) => {
    db.prepare("DELETE FROM issuances WHERE id = ?").run(req.params.id);
    res.json({ success: true });
  });

  app.get("/api/categories", (req, res) => {
    const rows = db.prepare("SELECT * FROM issuance_categories").all();
    res.json(rows);
  });

  app.post("/api/categories", (req, res) => {
    const { name } = req.body;
    try {
      const info = db.prepare("INSERT INTO issuance_categories (name) VALUES (?)").run(name);
      res.json({ id: info.lastInsertRowid });
    } catch (e) {
      res.status(400).json({ success: false, error: "Category already exists." });
    }
  });

  app.delete("/api/categories/:id", (req, res) => {
    db.prepare("DELETE FROM issuance_categories WHERE id = ?").run(req.params.id);
    res.json({ success: true });
  });

  app.get("/api/materials", (req, res) => {
    const rows = db.prepare("SELECT * FROM materials ORDER BY date DESC").all();
    res.json(rows);
  });

  app.post("/api/materials", (req, res) => {
    const { title, type, date, division, url } = req.body;
    const info = db.prepare("INSERT INTO materials (title, type, date, division, url) VALUES (?, ?, ?, ?, ?)").run(title, type, date, division, url);
    res.json({ id: info.lastInsertRowid });
  });

  app.get("/api/divisions", (req, res) => {
    const rows = db.prepare("SELECT * FROM divisions").all();
    res.json(rows);
  });

  app.post("/api/divisions", (req, res) => {
    const { name, description, head } = req.body;
    const info = db.prepare("INSERT INTO divisions (name, description, head) VALUES (?, ?, ?)").run(name, description, head);
    res.json({ id: info.lastInsertRowid });
  });

  app.delete("/api/divisions/:id", (req, res) => {
    db.prepare("DELETE FROM divisions WHERE id = ?").run(req.params.id);
    res.json({ success: true });
  });

  app.get("/api/dost-dx", (req, res) => {
    const rows = db.prepare("SELECT * FROM dost_dx").all();
    res.json(rows);
  });

  app.post("/api/dost-dx", (req, res) => {
    const { category, title, description } = req.body;
    const info = db.prepare("INSERT INTO dost_dx (category, title, description) VALUES (?, ?, ?)").run(category, title, description);
    res.json({ id: info.lastInsertRowid });
  });

  app.delete("/api/dost-dx/:id", (req, res) => {
    db.prepare("DELETE FROM dost_dx WHERE id = ?").run(req.params.id);
    res.json({ success: true });
  });

  app.post("/api/subscribe", (req, res) => {
    const { email } = req.body;
    try {
      db.prepare("INSERT INTO subscriptions (email) VALUES (?)").run(email);
      res.json({ success: true });
    } catch (e) {
      res.status(400).json({ success: false, error: "Email already subscribed or invalid." });
    }
  });

  // Simple Admin Auth (In a real app, use JWT and proper hashing)
  app.post("/api/login", (req, res) => {
    const { password } = req.body;
    if (password === "admin123") {
      res.json({ success: true, token: "mock-token" });
    } else {
      res.status(401).json({ success: false });
    }
  });

  // Vite middleware for development
  if (process.env.NODE_ENV !== "production") {
    const vite = await createViteServer({
      server: { middlewareMode: true },
      appType: "spa",
    });
    app.use(vite.middlewares);
  } else {
    app.use(express.static(path.join(__dirname, "dist")));
    app.get("*", (req, res) => {
      res.sendFile(path.join(__dirname, "dist", "index.html"));
    });
  }

  app.listen(PORT, "0.0.0.0", () => {
    console.log(`Server running on http://localhost:${PORT}`);
  });
}

startServer();
