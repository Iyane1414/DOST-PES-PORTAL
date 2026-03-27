import React, { useState, useEffect, useRef } from 'react';
import { BrowserRouter as Router, Routes, Route, Link, useNavigate, useLocation } from 'react-router-dom';
import { motion, AnimatePresence, useScroll, useTransform, useSpring, useMotionValue } from 'motion/react';
import { 
  Search, 
  Menu, 
  X, 
  ChevronRight, 
  FileText, 
  Layers, 
  Cpu, 
  Info, 
  MessageSquare, 
  ArrowRight, 
  Download, 
  Filter,
  LayoutDashboard,
  LogOut,
  Plus,
  Trash2,
  ExternalLink,
  Zap,
  Shield,
  Activity,
  Share2,
  Moon,
  Sun,
  TrendingUp,
  BarChart3,
  MousePointer2,
  Sparkles,
  Calendar
} from 'lucide-react';
import confetti from 'canvas-confetti';
import { 
  BarChart, 
  Bar, 
  XAxis, 
  YAxis, 
  CartesianGrid, 
  Tooltip, 
  ResponsiveContainer,
  Cell,
  PieChart,
  Pie
} from 'recharts';
import { cn } from './utils';
import { getGeminiResponse } from './services/geminiService';

// --- Types ---
interface Issuance {
  id: number;
  title: string;
  category: string;
  date: string;
  division: string;
  url: string;
}

interface Material {
  id: number;
  title: string;
  type: string;
  date: string;
  division: string;
  url: string;
}

interface Division {
  id: number;
  name: string;
  description: string;
  head: string;
}

interface DXContent {
  id: number;
  category: string;
  title: string;
  description: string;
}

interface Category {
  id: number;
  name: string;
}

// --- Components ---

const CustomCursor = () => {
  const [mousePos, setMousePos] = useState({ x: 0, y: 0 });
  const [isHovering, setIsHovering] = useState(false);

  useEffect(() => {
    const handleMouseMove = (e: MouseEvent) => {
      setMousePos({ x: e.clientX, y: e.clientY });
    };

    const handleMouseOver = (e: MouseEvent) => {
      const target = e.target as HTMLElement;
      if (target.closest('button, a, input, select, .cursor-pointer')) {
        setIsHovering(true);
      } else {
        setIsHovering(false);
      }
    };

    window.addEventListener('mousemove', handleMouseMove);
    window.addEventListener('mouseover', handleMouseOver);
    return () => {
      window.removeEventListener('mousemove', handleMouseMove);
      window.removeEventListener('mouseover', handleMouseOver);
    };
  }, []);

  return (
    <motion.div
      className="fixed top-0 left-0 w-6 h-6 bg-cyan-blue/30 rounded-full pointer-events-none z-[9999] mix-blend-difference hidden md:block"
      animate={{
        x: mousePos.x - 12,
        y: mousePos.y - 12,
        scale: isHovering ? 2.5 : 1,
        backgroundColor: isHovering ? 'rgba(0, 174, 239, 0.5)' : 'rgba(0, 174, 239, 0.3)'
      }}
      transition={{ type: 'spring', damping: 20, stiffness: 250, mass: 0.5 }}
    />
  );
};

const Reveal = ({ children, width = "100%" }: { children: React.ReactNode, width?: "100%" | "fit-content" }) => {
  return (
    <div style={{ position: "relative", width, overflow: "hidden" }}>
      <motion.div
        variants={{
          hidden: { opacity: 0, y: 75 },
          visible: { opacity: 1, y: 0 },
        }}
        initial="hidden"
        whileInView="visible"
        transition={{ duration: 0.5, delay: 0.25 }}
        viewport={{ once: true }}
      >
        {children}
      </motion.div>
    </div>
  );
};

const Navbar = () => {
  const [isScrolled, setIsScrolled] = useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const [isDarkMode, setIsDarkMode] = useState(false);

  useEffect(() => {
    const handleScroll = () => setIsScrolled(window.scrollY > 50);
    window.addEventListener('scroll', handleScroll);
    
    // Check system preference or local storage
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      setIsDarkMode(true);
      document.documentElement.classList.add('dark');
    }
    
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const toggleDarkMode = () => {
    const newMode = !isDarkMode;
    setIsDarkMode(newMode);
    if (newMode) {
      document.documentElement.classList.add('dark');
      localStorage.setItem('theme', 'dark');
    } else {
      document.documentElement.classList.remove('dark');
      localStorage.setItem('theme', 'light');
    }
  };

  const navLinks = [
    { name: 'Mandate', href: '#mandate' },
    { name: 'Divisions', href: '#divisions' },
    { name: 'Issuances', href: '#issuances' },
    { name: 'Materials', href: '#materials' },
    { name: 'DOST DX', href: '#dost-dx' },
    { name: 'Contact', href: '#contact' },
  ];

  return (
    <nav className={cn(
      "fixed top-0 w-full z-50 transition-all duration-300",
      isScrolled ? "glass py-3" : "bg-transparent py-5"
    )}>
      <div className="max-w-7xl mx-auto px-6 flex justify-between items-center">
        <Link to="/" className="text-xl font-semibold tracking-tight flex items-center gap-2">
          <div className="w-8 h-8 bg-cyan-blue rounded-lg flex items-center justify-center text-white font-bold">P</div>
          <span>PES <span className="text-cyan-blue">Portal</span></span>
        </Link>

        {/* Desktop Nav */}
        <div className="hidden md:flex items-center gap-8">
          {navLinks.map((link) => (
            <a 
              key={link.name} 
              href={link.href} 
              className="text-sm font-medium text-black/70 dark:text-white/70 hover:text-cyan-blue transition-colors"
            >
              {link.name}
            </a>
          ))}
          
          <button 
            onClick={toggleDarkMode}
            className="p-2 rounded-full hover:bg-black/5 dark:hover:bg-white/10 transition-all text-black/70 dark:text-white/70"
          >
            {isDarkMode ? <Sun size={18} /> : <Moon size={18} />}
          </button>

          <Link to="/admin" className="text-sm font-medium bg-black dark:bg-white dark:text-black text-white px-4 py-1.5 rounded-full hover:bg-black/80 transition-all">
            Admin
          </Link>
        </div>

        {/* Mobile Toggle */}
        <button className="md:hidden" onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}>
          {isMobileMenuOpen ? <X /> : <Menu />}
        </button>
      </div>

      {/* Mobile Menu */}
      <AnimatePresence>
        {isMobileMenuOpen && (
          <motion.div 
            initial={{ opacity: 0, y: -20 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -20 }}
            className="absolute top-full left-0 w-full bg-white border-b border-black/5 p-6 md:hidden flex flex-col gap-4"
          >
            {navLinks.map((link) => (
              <a 
                key={link.name} 
                href={link.href} 
                className="text-lg font-medium"
                onClick={() => setIsMobileMenuOpen(false)}
              >
                {link.name}
              </a>
            ))}
            <Link to="/admin" className="text-lg font-medium text-cyan-blue" onClick={() => setIsMobileMenuOpen(false)}>
              Admin Dashboard
            </Link>
          </motion.div>
        )}
      </AnimatePresence>
    </nav>
  );
};

const Hero = () => {
  const { scrollY } = useScroll();
  const y1 = useTransform(scrollY, [0, 500], [0, 200]);
  const y2 = useTransform(scrollY, [0, 500], [0, -150]);
  const opacity = useTransform(scrollY, [0, 300], [1, 0]);
  const rotate = useTransform(scrollY, [0, 500], [0, 45]);

  return (
    <section className="relative h-screen flex flex-col items-center justify-center text-center overflow-hidden bg-white dark:bg-dark-bg transition-colors">
      {/* Animated Background Elements */}
      <motion.div 
        style={{ y: y2, rotate }}
        className="absolute top-20 left-20 w-64 h-64 bg-cyan-blue/5 rounded-full blur-3xl -z-0"
      />
      <motion.div 
        style={{ y: y1, rotate: -rotate }}
        className="absolute bottom-20 right-20 w-96 h-96 bg-cyan-blue/10 rounded-full blur-3xl -z-0"
      />

      <motion.div style={{ y: y1, opacity }} className="z-10 px-6">
        <Reveal width="fit-content">
          <span className="text-cyan-blue font-semibold tracking-widest uppercase text-xs mb-4 block mx-auto">
            Planning and Evaluation Service
          </span>
        </Reveal>
        
        <Reveal>
          <h1 className="text-5xl md:text-8xl font-bold tracking-tighter mb-6 max-w-5xl dark:text-white leading-[0.9]">
            Proactive Planning. <br />
            <span className="text-black/20 dark:text-white/20">Precise Evaluation.</span>
          </h1>
        </Reveal>

        <Reveal>
          <p className="text-lg md:text-xl text-black/60 dark:text-white/60 max-w-2xl mx-auto mb-10">
            Empowering DOST through strategic foresight and data-driven insights. 
            The central hub for PES mandates, issuances, and digital transformation.
          </p>
        </Reveal>

        <motion.div 
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.8 }}
          className="flex flex-col sm:flex-row gap-4 justify-center"
        >
          <a href="#mandate" className="bg-cyan-blue text-white px-8 py-4 rounded-full font-bold hover:bg-cyan-blue/90 transition-all flex items-center justify-center gap-2 group">
            Explore Mandate <ChevronRight size={18} className="group-hover:translate-x-1 transition-transform" />
          </a>
          <a href="#dost-dx" className="bg-black dark:bg-white dark:text-black text-white px-8 py-4 rounded-full font-bold hover:bg-black/80 transition-all flex items-center justify-center gap-2 group">
            DOST DX Initiatives <Zap size={18} className="group-hover:scale-110 transition-transform" />
          </a>
        </motion.div>
      </motion.div>

      {/* Background Decorative Element */}
      <div className="absolute bottom-0 left-0 w-full h-1/2 bg-gradient-to-t from-apple-gray dark:from-dark-card/20 to-transparent -z-0" />
      
      <motion.div 
        animate={{ y: [0, 10, 0] }}
        transition={{ repeat: Infinity, duration: 2 }}
        className="absolute bottom-10 left-1/2 -translate-x-1/2 text-black/20 dark:text-white/20"
      >
        <div className="w-6 h-10 border-2 border-current rounded-full flex justify-center p-1">
          <motion.div 
            animate={{ y: [0, 12, 0] }}
            transition={{ repeat: Infinity, duration: 1.5 }}
            className="w-1 h-2 bg-current rounded-full" 
          />
        </div>
      </motion.div>
    </section>
  );
};

const WhatsNewSection = () => {
  const [latest, setLatest] = useState<(Issuance | Material)[]>([]);
  const [currentIndex, setCurrentIndex] = useState(0);
  const [isPaused, setIsPaused] = useState(false);

  useEffect(() => {
    Promise.all([
      fetch('/api/issuances').then(res => res.json()),
      fetch('/api/materials').then(res => res.json())
    ]).then(([issuances, materials]) => {
      const combined = [...issuances, ...materials]
        .sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime())
        .slice(0, 10);
      setLatest(combined);
    });
  }, []);

  useEffect(() => {
    if (latest.length === 0 || isPaused) return;
    const timer = setInterval(() => {
      setCurrentIndex((prev) => (prev + 1) % latest.length);
    }, 5000);
    return () => clearInterval(timer);
  }, [latest, isPaused]);

  const next = () => setCurrentIndex((prev) => (prev + 1) % latest.length);
  const prev = () => setCurrentIndex((prev) => (prev - 1 + latest.length) % latest.length);

  return (
    <section className="section-padding bg-white dark:bg-dark-bg transition-colors overflow-hidden">
      <div className="max-w-7xl mx-auto">
        <Reveal>
          <div className="flex justify-between items-end mb-12">
            <div>
              <h2 className="text-3xl font-bold tracking-tight mb-2 dark:text-white">What's New</h2>
              <p className="text-black/40 dark:text-white/40">Stay updated with the latest from PES.</p>
            </div>
            <div className="flex items-center gap-4">
              <div className="flex gap-2">
                <button 
                  onClick={prev}
                  className="p-2 rounded-full bg-apple-gray dark:bg-dark-card hover:bg-cyan-blue hover:text-white transition-all"
                >
                  <ChevronRight size={20} className="rotate-180" />
                </button>
                <button 
                  onClick={next}
                  className="p-2 rounded-full bg-apple-gray dark:bg-dark-card hover:bg-cyan-blue hover:text-white transition-all"
                >
                  <ChevronRight size={20} />
                </button>
              </div>
              <a href="#issuances" className="text-cyan-blue font-medium flex items-center gap-1 hover:underline">
                View all <ArrowRight size={16} />
              </a>
            </div>
          </div>
        </Reveal>

        <div 
          className="relative h-[250px] md:h-[200px]"
          onMouseEnter={() => setIsPaused(true)}
          onMouseLeave={() => setIsPaused(false)}
        >
          <AnimatePresence mode="wait">
            {latest.length > 0 && (
              <motion.div
                key={currentIndex}
                initial={{ opacity: 0, x: 50 }}
                animate={{ opacity: 1, x: 0 }}
                exit={{ opacity: 0, x: -50 }}
                transition={{ type: "spring", stiffness: 300, damping: 30 }}
                className="absolute inset-0"
              >
                {(() => {
                  const item = latest[currentIndex];
                  return (
                    <motion.div 
                      whileHover={{ y: -5 }}
                      className="p-8 md:p-10 bg-apple-gray dark:bg-dark-card rounded-[2.5rem] border border-black/5 dark:border-white/5 hover:shadow-2xl transition-all group h-full flex flex-col md:flex-row md:items-center justify-between gap-6"
                    >
                      <div className="flex-1">
                        <div className="flex items-center gap-2 mb-4">
                          <div className="w-2 h-2 bg-cyan-blue rounded-full animate-pulse" />
                          <span className="text-[10px] font-bold uppercase tracking-widest text-black/40 dark:text-white/40">
                            {'category' in item ? 'Issuance' : 'Material'}
                          </span>
                        </div>
                        <h3 className="text-2xl md:text-3xl font-bold dark:text-white group-hover:text-cyan-blue transition-colors line-clamp-2">{item.title}</h3>
                      </div>
                      
                      <div className="flex flex-row md:flex-col items-center md:items-end justify-between md:justify-center gap-4 shrink-0">
                        <span className="text-sm font-medium text-black/50 dark:text-white/50">{item.date}</span>
                        <a 
                          href={item.url} 
                          className="bg-cyan-blue text-white px-8 py-3 rounded-2xl font-bold hover:bg-cyan-blue/90 transition-all shadow-lg shadow-cyan-blue/20"
                        >
                          View Details
                        </a>
                      </div>
                    </motion.div>
                  );
                })()}
              </motion.div>
            )}
          </AnimatePresence>
        </div>

        <div className="flex justify-center gap-2 mt-12">
          {latest.map((_, idx) => (
            <button
              key={idx}
              onClick={() => setCurrentIndex(idx)}
              className={cn(
                "w-2 h-2 rounded-full transition-all",
                currentIndex === idx ? "w-8 bg-cyan-blue" : "bg-black/10 dark:bg-white/10 hover:bg-cyan-blue/50"
              )}
            />
          ))}
        </div>
      </div>
    </section>
  );
};

const ContactSection = () => {
  const [formData, setFormData] = useState({ name: '', email: '', subject: '', message: '' });
  const [status, setStatus] = useState<'idle' | 'loading' | 'success' | 'error'>('idle');

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setStatus('loading');
    // Simulate API call
    setTimeout(() => {
      setStatus('success');
      setFormData({ name: '', email: '', subject: '', message: '' });
      setTimeout(() => setStatus('idle'), 3000);
    }, 1500);
  };

  return (
    <section id="contact" className="section-padding bg-white dark:bg-dark-bg transition-colors">
      <div className="max-w-7xl mx-auto">
        <Reveal>
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold tracking-tight mb-4">Get in Touch</h2>
            <p className="text-xl text-black/50 dark:text-white/50">Have questions? We're here to help.</p>
          </div>
        </Reveal>

        <div className="grid lg:grid-cols-2 gap-16">
          <motion.div 
            initial={{ opacity: 0, x: -50 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            className="space-y-12"
          >
            <div>
              <h3 className="text-2xl font-bold mb-6">Contact Information</h3>
              <div className="space-y-6">
                <div className="flex items-start gap-4">
                  <div className="w-12 h-12 bg-cyan-blue/10 text-cyan-blue rounded-2xl flex items-center justify-center shrink-0">
                    <Info size={24} />
                  </div>
                  <div>
                    <div className="font-bold mb-1">Office Address</div>
                    <p className="text-black/60 dark:text-white/60">DOST Complex, Gen. Santos Ave., Bicutan, Taguig City, Philippines</p>
                  </div>
                </div>
                <div className="flex items-start gap-4">
                  <div className="w-12 h-12 bg-cyan-blue/10 text-cyan-blue rounded-2xl flex items-center justify-center shrink-0">
                    <Activity size={24} />
                  </div>
                  <div>
                    <div className="font-bold mb-1">Phone Number</div>
                    <p className="text-black/60 dark:text-white/60">+63 (2) 8837-2071 to 82</p>
                  </div>
                </div>
                <div className="flex items-start gap-4">
                  <div className="w-12 h-12 bg-cyan-blue/10 text-cyan-blue rounded-2xl flex items-center justify-center shrink-0">
                    <MessageSquare size={24} />
                  </div>
                  <div>
                    <div className="font-bold mb-1">Email Address</div>
                    <p className="text-black/60 dark:text-white/60">pes@dost.gov.ph</p>
                  </div>
                </div>
              </div>
            </div>

            <div className="p-8 bg-apple-gray dark:bg-dark-card rounded-[2.5rem] border border-black/5 dark:border-white/5">
              <h4 className="font-bold mb-4">Office Hours</h4>
              <div className="space-y-2 text-black/60 dark:text-white/60">
                <div className="flex justify-between">
                  <span>Monday - Friday</span>
                  <span>8:00 AM - 5:00 PM</span>
                </div>
                <div className="flex justify-between">
                  <span>Saturday - Sunday</span>
                  <span>Closed</span>
                </div>
              </div>
            </div>
          </motion.div>

          <motion.div 
            initial={{ opacity: 0, x: 50 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            className="bg-apple-gray dark:bg-dark-card p-10 rounded-[3rem] border border-black/5 dark:border-white/5 shadow-sm"
          >
            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="grid sm:grid-cols-2 gap-6">
                <div className="space-y-2">
                  <label className="text-sm font-bold uppercase tracking-widest text-black/40 dark:text-white/40">Full Name</label>
                  <input 
                    type="text" 
                    required
                    className="w-full px-6 py-4 bg-white dark:bg-white/5 rounded-2xl border border-black/5 dark:border-white/5 focus:outline-none focus:ring-2 focus:ring-cyan-blue/20 dark:text-white"
                    placeholder="Juan Dela Cruz"
                    value={formData.name}
                    onChange={(e) => setFormData({...formData, name: e.target.value})}
                  />
                </div>
                <div className="space-y-2">
                  <label className="text-sm font-bold uppercase tracking-widest text-black/40 dark:text-white/40">Email Address</label>
                  <input 
                    type="email" 
                    required
                    className="w-full px-6 py-4 bg-white dark:bg-white/5 rounded-2xl border border-black/5 dark:border-white/5 focus:outline-none focus:ring-2 focus:ring-cyan-blue/20 dark:text-white"
                    placeholder="juan@example.com"
                    value={formData.email}
                    onChange={(e) => setFormData({...formData, email: e.target.value})}
                  />
                </div>
              </div>
              <div className="space-y-2">
                <label className="text-sm font-bold uppercase tracking-widest text-black/40 dark:text-white/40">Subject</label>
                <input 
                  type="text" 
                  required
                  className="w-full px-6 py-4 bg-white dark:bg-white/5 rounded-2xl border border-black/5 dark:border-white/5 focus:outline-none focus:ring-2 focus:ring-cyan-blue/20 dark:text-white"
                  placeholder="How can we help?"
                  value={formData.subject}
                  onChange={(e) => setFormData({...formData, subject: e.target.value})}
                />
              </div>
              <div className="space-y-2">
                <label className="text-sm font-bold uppercase tracking-widest text-black/40 dark:text-white/40">Message</label>
                <textarea 
                  required
                  rows={4}
                  className="w-full px-6 py-4 bg-white dark:bg-white/5 rounded-2xl border border-black/5 dark:border-white/5 focus:outline-none focus:ring-2 focus:ring-cyan-blue/20 dark:text-white resize-none"
                  placeholder="Your message here..."
                  value={formData.message}
                  onChange={(e) => setFormData({...formData, message: e.target.value})}
                />
              </div>
              <button 
                type="submit"
                disabled={status === 'loading'}
                className="w-full bg-cyan-blue text-white py-5 rounded-2xl font-bold hover:bg-cyan-blue/90 transition-all flex items-center justify-center gap-2 shadow-lg shadow-cyan-blue/20 disabled:opacity-50"
              >
                {status === 'loading' ? (
                  <div className="w-6 h-6 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                ) : status === 'success' ? (
                  <>Message Sent! <Sparkles size={20} /></>
                ) : (
                  <>Send Message <ArrowRight size={20} /></>
                )}
              </button>
            </form>
          </motion.div>
        </div>
      </div>
    </section>
  );
};

const SubscriptionSection = () => {
  const [email, setEmail] = useState('');
  const [status, setStatus] = useState<'idle' | 'loading' | 'success' | 'error'>('idle');

  const handleSubscribe = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!email) return;
    setStatus('loading');
    const res = await fetch('/api/subscribe', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email })
    });
    if (res.ok) {
      setStatus('success');
      setEmail('');
      confetti({
        particleCount: 150,
        spread: 70,
        origin: { y: 0.6 },
        colors: ['#00AEEF', '#000000', '#ffffff']
      });
    } else {
      setStatus('error');
    }
  };

  return (
    <section className="section-padding bg-cyan-blue text-white overflow-hidden relative">
      <div className="max-w-7xl mx-auto relative z-10 flex flex-col md:flex-row items-center justify-between gap-12">
        <div className="max-w-xl">
          <h2 className="text-4xl font-bold tracking-tight mb-6">Stay proactive with PES.</h2>
          <p className="text-white/80 text-lg">
            Subscribe to our newsletter to receive real-time notifications about new issuances, materials, and DOST DX updates.
          </p>
        </div>
        
        <form onSubmit={handleSubscribe} className="w-full max-w-md">
          <div className="flex p-2 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20">
            <input 
              type="email" 
              placeholder="Enter your email"
              className="flex-1 bg-transparent px-4 py-3 text-white placeholder:text-white/50 focus:outline-none"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
            />
            <button 
              type="submit"
              disabled={status === 'loading'}
              className="bg-white text-cyan-blue px-8 py-3 rounded-xl font-bold hover:bg-white/90 transition-all disabled:opacity-50"
            >
              {status === 'loading' ? 'Subscribing...' : 'Subscribe'}
            </button>
          </div>
          {status === 'success' && <p className="mt-4 text-sm font-medium">Thank you for subscribing!</p>}
          {status === 'error' && <p className="mt-4 text-sm font-medium text-red-200">Something went wrong. Please try again.</p>}
        </form>
      </div>
      
      {/* Abstract background shapes */}
      <div className="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl" />
      <div className="absolute bottom-0 left-0 w-64 h-64 bg-black/5 rounded-full translate-y-1/2 -translate-x-1/2 blur-2xl" />
    </section>
  );
};

const MandateSection = () => {
  return (
    <section id="mandate" className="section-padding bg-apple-gray dark:bg-dark-bg/30 transition-colors">
      <div className="max-w-7xl mx-auto grid md:grid-cols-2 gap-16 items-center">
        <motion.div 
          initial={{ opacity: 0, x: -50 }}
          whileInView={{ opacity: 1, x: 0 }}
          viewport={{ once: true }}
        >
          <Reveal>
            <h2 className="text-4xl md:text-5xl font-bold tracking-tight mb-8 dark:text-white">Our Mandate</h2>
          </Reveal>
          <Reveal>
            <p className="text-xl text-black/70 dark:text-white/70 leading-relaxed mb-6">
              The Planning and Evaluation Service (PES) is the primary arm of the Department of Science and Technology (DOST) 
              responsible for the formulation of long-term and short-term strategic plans.
            </p>
          </Reveal>
          <Reveal>
            <p className="text-lg text-black/60 dark:text-white/60 leading-relaxed">
              Headed by <span className="text-black dark:text-white font-semibold">Dir. Pedraza</span>, PES ensures that all DOST programs 
              are aligned with national priorities and are evaluated based on rigorous impact assessment frameworks.
            </p>
          </Reveal>
          
          <div className="mt-12 grid grid-cols-2 gap-6">
            <motion.div 
              whileHover={{ y: -5 }}
              className="p-6 bg-white dark:bg-dark-card rounded-3xl shadow-sm border border-black/5 dark:border-white/5"
            >
              <div className="w-10 h-10 bg-cyan-blue/10 text-cyan-blue rounded-xl flex items-center justify-center mb-4">
                <Activity size={20} />
              </div>
              <h3 className="font-bold mb-2 dark:text-white">Strategic Planning</h3>
              <p className="text-sm text-black/50 dark:text-white/40">Formulating the DOST roadmap for the future.</p>
            </motion.div>
            <motion.div 
              whileHover={{ y: -5 }}
              className="p-6 bg-white dark:bg-dark-card rounded-3xl shadow-sm border border-black/5 dark:border-white/5"
            >
              <div className="w-10 h-10 bg-cyan-blue/10 text-cyan-blue rounded-xl flex items-center justify-center mb-4">
                <Shield size={20} />
              </div>
              <h3 className="font-bold mb-2 dark:text-white">Impact Evaluation</h3>
              <p className="text-sm text-black/50 dark:text-white/40">Measuring the real-world success of S&T initiatives.</p>
            </motion.div>
          </div>
        </motion.div>

        <motion.div 
          initial={{ opacity: 0, scale: 0.9 }}
          whileInView={{ opacity: 1, scale: 1 }}
          viewport={{ once: true }}
          className="relative"
        >
          <div className="aspect-square bg-white dark:bg-dark-card rounded-[3rem] shadow-2xl overflow-hidden flex items-center justify-center p-12 border border-black/5 dark:border-white/5">
            <div className="text-center">
              <div className="text-8xl font-black text-cyan-blue/10 mb-4">DOST</div>
              <div className="text-3xl font-bold tracking-tighter dark:text-white">PLANNING & EVALUATION</div>
              <div className="text-cyan-blue font-medium mt-2">SERVICE</div>
            </div>
          </div>
          {/* Floating elements */}
          <motion.div 
            animate={{ y: [0, -20, 0] }}
            transition={{ repeat: Infinity, duration: 4 }}
            className="absolute -top-6 -right-6 p-6 bg-cyan-blue text-white rounded-2xl shadow-xl"
          >
            <Zap />
          </motion.div>
        </motion.div>
      </div>
    </section>
  );
};

const DivisionsSection = () => {
  const [divisions, setDivisions] = useState<Division[]>([]);
  const [selectedDivision, setSelectedDivision] = useState<Division | null>(null);

  useEffect(() => {
    fetch('/api/divisions').then(res => res.json()).then(setDivisions);
  }, []);

  return (
    <section id="divisions" className="section-padding bg-white dark:bg-dark-bg transition-colors">
      <div className="max-w-7xl mx-auto">
        <Reveal>
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold tracking-tight mb-4">PES Divisions</h2>
            <p className="text-xl text-black/50 dark:text-white/50">Specialized units working in harmony.</p>
          </div>
        </Reveal>

        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          {divisions.map((div, idx) => (
            <DivisionCard key={div.id} div={div} idx={idx} onClick={() => setSelectedDivision(div)} />
          ))}
        </div>
      </div>
      {/* ... modal code ... */}

      {/* Division Detail Modal */}
      <AnimatePresence>
        {selectedDivision && (
          <div className="fixed inset-0 z-[100] flex items-center justify-center p-6">
            <motion.div 
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              onClick={() => setSelectedDivision(null)}
              className="absolute inset-0 bg-black/60 backdrop-blur-sm"
            />
            <motion.div 
              initial={{ opacity: 0, scale: 0.9, y: 20 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.9, y: 20 }}
              className="relative w-full max-w-2xl bg-white dark:bg-dark-card rounded-[3rem] shadow-2xl overflow-hidden"
            >
              <button 
                onClick={() => setSelectedDivision(null)}
                className="absolute top-8 right-8 p-2 rounded-full bg-apple-gray dark:bg-white/10 hover:bg-black hover:text-white transition-all"
              >
                <X size={20} />
              </button>
              
              <div className="p-12">
                <div className="w-16 h-16 bg-cyan-blue/10 text-cyan-blue rounded-2xl flex items-center justify-center mb-8">
                  <Layers size={32} />
                </div>
                <h2 className="text-4xl font-bold mb-4">{selectedDivision.name}</h2>
                <div className="flex items-center gap-2 text-cyan-blue font-bold mb-8">
                  <Shield size={18} /> Head: {selectedDivision.head || 'To be announced'}
                </div>
                <div className="space-y-6 text-lg text-black/60 dark:text-white/60 leading-relaxed">
                  <p>{selectedDivision.description}</p>
                  <p>
                    This division plays a critical role in the overall PES mandate, ensuring that DOST's strategic goals are met through rigorous processes and inter-division collaboration.
                  </p>
                </div>
                
                <div className="mt-12 pt-8 border-t border-black/5 dark:border-white/5 flex gap-4">
                  <button className="bg-black dark:bg-white dark:text-black text-white px-8 py-3 rounded-full font-bold hover:opacity-80 transition-all">
                    View Projects
                  </button>
                  <button className="bg-apple-gray dark:bg-white/10 px-8 py-3 rounded-full font-bold hover:bg-black hover:text-white transition-all">
                    Contact Division
                  </button>
                </div>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </section>
  );
};

const IssuancesSection = () => {
  const [issuances, setIssuances] = useState<Issuance[]>([]);
  const [categories, setCategories] = useState<Category[]>([]);
  const [search, setSearch] = useState('');
  const [category, setCategory] = useState('All');

  useEffect(() => {
    fetch('/api/issuances').then(res => res.json()).then(setIssuances);
    fetch('/api/categories').then(res => res.json()).then(setCategories);
  }, []);

  const filtered = issuances.filter(i => {
    const matchesSearch = i.title.toLowerCase().includes(search.toLowerCase()) || i.division.toLowerCase().includes(search.toLowerCase());
    const matchesCat = category === 'All' || i.category === category;
    return matchesSearch && matchesCat;
  });

  const categoryNames = ['All', ...categories.map(c => c.name)];

  const handleShare = async (title: string, url: string) => {
    const shareData = {
      title: `PES Portal: ${title}`,
      text: `Check out this document from the DOST PES Portal: ${title}`,
      url: window.location.origin + url
    };

    try {
      if (navigator.share) {
        await navigator.share(shareData);
      } else {
        await navigator.clipboard.writeText(shareData.url);
        alert('Link copied to clipboard!');
      }
    } catch (err) {
      console.error('Error sharing:', err);
    }
  };

  return (
    <section id="issuances" className="section-padding bg-apple-gray dark:bg-dark-bg/30 transition-colors">
      <div className="max-w-7xl mx-auto">
        <Reveal>
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
            <div>
              <h2 className="text-4xl font-bold tracking-tight mb-2 dark:text-white">PES Issuances</h2>
              <p className="text-black/50 dark:text-white/40">Official memos, letters, and orders.</p>
            </div>
            
            <div className="flex flex-col sm:flex-row gap-4">
              <div className="relative">
                <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-black/30 dark:text-white/30" size={18} />
                <input 
                  type="text" 
                  placeholder="Search issuances..."
                  className="pl-12 pr-6 py-3 bg-white dark:bg-dark-card rounded-full border border-black/5 dark:border-white/5 focus:outline-none focus:ring-2 focus:ring-cyan-blue/20 w-full sm:w-64 dark:text-white"
                  value={search}
                  onChange={(e) => setSearch(e.target.value)}
                />
              </div>
              <div className="flex gap-2 overflow-x-auto pb-2 sm:pb-0 no-scrollbar">
                {categoryNames.map(cat => (
                  <button 
                    key={cat}
                    onClick={() => setCategory(cat)}
                    className={cn(
                      "px-5 py-3 rounded-full text-sm font-medium transition-all whitespace-nowrap",
                      category === cat ? "bg-black dark:bg-cyan-blue text-white" : "bg-white dark:bg-white/10 text-black/60 dark:text-white/60 hover:bg-black/5 dark:hover:bg-white/20"
                    )}
                  >
                    {cat}
                  </button>
                ))}
              </div>
            </div>
          </div>
        </Reveal>

        <div className="bg-white dark:bg-dark-card rounded-[2.5rem] shadow-sm border border-black/5 dark:border-white/5 overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full text-left">
              <thead>
                <tr className="border-b border-black/5 dark:border-white/5">
                  <th className="px-8 py-6 text-sm font-semibold text-black/40 dark:text-white/30">Title</th>
                  <th className="px-8 py-6 text-sm font-semibold text-black/40 dark:text-white/30">Category</th>
                  <th className="px-8 py-6 text-sm font-semibold text-black/40 dark:text-white/30">Division</th>
                  <th className="px-8 py-6 text-sm font-semibold text-black/40 dark:text-white/30">Date</th>
                  <th className="px-8 py-6 text-sm font-semibold text-black/40 dark:text-white/30 text-right">Action</th>
                </tr>
              </thead>
              <tbody>
                {filtered.map((item) => (
                  <tr key={item.id} className="border-b border-black/5 dark:border-white/5 last:border-0 hover:bg-apple-gray/50 dark:hover:bg-white/5 transition-colors group">
                    <td className="px-8 py-6">
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-cyan-blue/10 text-cyan-blue rounded-lg flex items-center justify-center">
                          <FileText size={18} />
                        </div>
                        <span className="font-medium dark:text-white">{item.title}</span>
                      </div>
                    </td>
                    <td className="px-8 py-6 text-black/60 dark:text-white/60">{item.category}</td>
                    <td className="px-8 py-6 text-black/60 dark:text-white/60">{item.division}</td>
                    <td className="px-8 py-6 text-black/60 dark:text-white/60">{item.date}</td>
                    <td className="px-8 py-6 text-right">
                      <div className="flex justify-end items-center gap-3">
                        <button 
                          onClick={() => handleShare(item.title, item.url)}
                          className="p-2 text-black/40 dark:text-white/40 hover:text-cyan-blue transition-colors"
                          title="Share"
                        >
                          <Share2 size={18} />
                        </button>
                        <a href={item.url} className="inline-flex items-center gap-2 text-cyan-blue font-medium hover:underline">
                          Download <Download size={16} />
                        </a>
                      </div>
                    </td>
                  </tr>
                ))}
                {filtered.length === 0 && (
                  <tr>
                    <td colSpan={5} className="px-8 py-20 text-center text-black/30 dark:text-white/20">
                      No issuances found matching your criteria.
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  );
};

const MaterialsSection = () => {
  const [materials, setMaterials] = useState<Material[]>([]);
  const [search, setSearch] = useState('');

  useEffect(() => {
    fetch('/api/materials').then(res => res.json()).then(setMaterials);
  }, []);

  const filtered = materials.filter(m => 
    m.title.toLowerCase().includes(search.toLowerCase()) || 
    m.type.toLowerCase().includes(search.toLowerCase())
  );

  const handleShare = async (title: string, url: string) => {
    const shareData = {
      title: `PES Portal: ${title}`,
      text: `Check out this resource from the DOST PES Portal: ${title}`,
      url: window.location.origin + url
    };

    try {
      if (navigator.share) {
        await navigator.share(shareData);
      } else {
        await navigator.clipboard.writeText(shareData.url);
        alert('Link copied to clipboard!');
      }
    } catch (err) {
      console.error('Error sharing:', err);
    }
  };

  return (
    <section id="materials" className="section-padding bg-white dark:bg-dark-bg transition-colors">
      <div className="max-w-7xl mx-auto">
        <Reveal>
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
            <div>
              <h2 className="text-4xl font-bold tracking-tight mb-2 dark:text-white">PES Materials</h2>
              <p className="text-black/50 dark:text-white/40">PowerPoints, videos, and infographics.</p>
            </div>
            
            <div className="relative">
              <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-black/30 dark:text-white/30" size={18} />
              <input 
                type="text" 
                placeholder="Search materials..."
                className="pl-12 pr-6 py-3 bg-apple-gray dark:bg-dark-card rounded-full border border-black/5 dark:border-white/5 focus:outline-none focus:ring-2 focus:ring-cyan-blue/20 w-full sm:w-64 dark:text-white"
                value={search}
                onChange={(e) => setSearch(e.target.value)}
              />
            </div>
          </div>
        </Reveal>

        <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
          {filtered.map((mat) => (
            <motion.div 
              key={mat.id}
              initial={{ opacity: 0, scale: 0.95 }}
              whileInView={{ opacity: 1, scale: 1 }}
              viewport={{ once: true }}
              className="group bg-apple-gray dark:bg-dark-card rounded-[2rem] p-8 hover:shadow-xl transition-all border border-black/5 dark:border-white/5"
            >
              <div className="flex justify-between items-start mb-6">
                <div className="w-12 h-12 bg-white dark:bg-white/10 text-cyan-blue rounded-2xl flex items-center justify-center shadow-sm">
                  {mat.type === 'Video' ? <Zap size={24} /> : <FileText size={24} />}
                </div>
                <span className="px-3 py-1 bg-white dark:bg-white/10 rounded-full text-[10px] font-bold uppercase tracking-wider text-black/40 dark:text-white/40">{mat.type}</span>
              </div>
              <h3 className="text-xl font-bold mb-2 group-hover:text-cyan-blue transition-colors dark:text-white">{mat.title}</h3>
              <p className="text-sm text-black/40 dark:text-white/40 mb-6">{mat.division} • {mat.date}</p>
              <div className="flex justify-between items-center mt-6">
                <a href={mat.url} className="inline-flex items-center gap-2 text-sm font-bold text-black dark:text-white hover:text-cyan-blue transition-colors">
                  View Resource <ChevronRight size={16} />
                </a>
                <button 
                  onClick={() => handleShare(mat.title, mat.url)}
                  className="p-2 text-black/40 dark:text-white/40 hover:text-cyan-blue transition-colors"
                  title="Share"
                >
                  <Share2 size={18} />
                </button>
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
};

interface DivisionCardProps {
  div: Division;
  idx: number;
  onClick: () => void;
}

const DivisionCard: React.FC<DivisionCardProps> = ({ div, idx, onClick }) => {
  const x = useMotionValue(0);
  const y = useMotionValue(0);

  const mouseXSpring = useSpring(x);
  const mouseYSpring = useSpring(y);

  const rotateX = useTransform(mouseYSpring, [-0.5, 0.5], ["17.5deg", "-17.5deg"]);
  const rotateY = useTransform(mouseXSpring, [-0.5, 0.5], ["-17.5deg", "17.5deg"]);

  const handleMouseMove = (e: React.MouseEvent<HTMLDivElement>) => {
    const rect = e.currentTarget.getBoundingClientRect();
    const width = rect.width;
    const height = rect.height;
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;
    const xPct = mouseX / width - 0.5;
    const yPct = mouseY / height - 0.5;
    x.set(xPct);
    y.set(yPct);
  };

  const handleMouseLeave = () => {
    x.set(0);
    y.set(0);
  };

  return (
    <motion.div 
      initial={{ opacity: 0, y: 30 }}
      whileInView={{ opacity: 1, y: 0 }}
      transition={{ delay: idx * 0.1 }}
      viewport={{ once: true }}
      onMouseMove={handleMouseMove}
      onMouseLeave={handleMouseLeave}
      style={{
        rotateY,
        rotateX,
        transformStyle: "preserve-3d",
      }}
      onClick={onClick}
      className="group relative p-8 bg-apple-gray dark:bg-dark-card rounded-[2.5rem] hover:bg-black hover:text-white dark:hover:bg-cyan-blue transition-all duration-500 cursor-pointer border border-black/5 dark:border-white/5"
    >
      <div style={{ transform: "translateZ(75px)", transformStyle: "preserve-3d" }}>
        <div className="w-12 h-12 bg-white dark:bg-white/10 group-hover:bg-cyan-blue text-cyan-blue group-hover:text-white rounded-2xl flex items-center justify-center mb-6 transition-colors">
          <Layers size={24} />
        </div>
        <h3 className="text-2xl font-bold mb-4">{div.name}</h3>
        <p className="text-black/60 dark:text-white/70 group-hover:text-white/70 leading-relaxed mb-6 line-clamp-3">
          {div.description}
        </p>
        <div className="flex items-center gap-2 text-sm font-medium">
          <span className="opacity-50">Headed by:</span>
          <span>{div.head}</span>
        </div>
        <div className="mt-6 flex items-center gap-2 text-cyan-blue font-bold text-sm opacity-0 group-hover:opacity-100 transition-all translate-y-2 group-hover:translate-y-0">
          Learn more <ArrowRight size={16} />
        </div>
      </div>
    </motion.div>
  );
};

const DOSTDXSection = () => {
  const [activeTab, setActiveTab] = useState<'domains' | 'programs'>('domains');
  const [isRoadmapOpen, setIsRoadmapOpen] = useState(false);
  
  const domains = [
    { title: "Digital Infrastructure", desc: "Modernizing the backbone of DOST operations with high-speed connectivity and cloud-native solutions.", icon: <Cpu />, color: "bg-blue-500" },
    { title: "Digital Governance", desc: "Streamlining processes through policy, automation, and data-driven decision making.", icon: <Shield />, color: "bg-cyan-500" },
    { title: "Digital Services", desc: "Delivering citizen-centric online platforms that are accessible, reliable, and secure.", icon: <Zap />, color: "bg-indigo-500" }
  ];

  const programs = [
    "E-Government Systems",
    "Data Analytics Hub",
    "Cybersecurity Framework",
    "Cloud Infrastructure",
    "Digital Literacy",
    "Smart Office Solutions"
  ];

  return (
    <section id="dost-dx" className="section-padding bg-black text-white overflow-hidden">
      <div className="max-w-7xl mx-auto">
        <div className="grid lg:grid-cols-2 gap-20 items-center mb-20">
          <div>
            <motion.span 
              initial={{ opacity: 0 }}
              whileInView={{ opacity: 1 }}
              className="text-cyan-blue font-bold tracking-widest uppercase text-xs mb-4 block"
            >
              The Future is Digital
            </motion.span>
            <h2 className="text-5xl md:text-6xl font-bold tracking-tighter mb-8">DOST Digital Transformation</h2>
            <p className="text-xl text-white/60 mb-10">
              DOST DX is our commitment to evolving into a data-driven, agile, and citizen-centric organization. We are redefining how science and technology serve the Filipino people.
            </p>
            <div className="flex gap-4">
              <button className="bg-cyan-blue text-white px-8 py-3 rounded-full font-bold hover:bg-cyan-blue/90 transition-all flex items-center gap-2 group">
                Learn More <ChevronRight size={18} className="group-hover:translate-x-1 transition-transform" />
              </button>
              <button 
                onClick={() => setIsRoadmapOpen(true)}
                className="bg-white/10 text-white px-8 py-3 rounded-full font-bold hover:bg-white/20 transition-all flex items-center gap-2 group"
              >
                DX Roadmap <Calendar size={18} className="group-hover:scale-110 transition-transform" />
              </button>
            </div>
          </div>
          
          <motion.div 
            initial={{ opacity: 0, scale: 0.9 }}
            whileInView={{ opacity: 1, scale: 1 }}
            className="relative aspect-video bg-white/5 rounded-[3rem] border border-white/10 overflow-hidden group cursor-pointer"
          >
            <div className="absolute inset-0 flex items-center justify-center z-10">
              <motion.div 
                whileHover={{ scale: 1.1 }}
                whileTap={{ scale: 0.9 }}
                className="w-20 h-20 bg-cyan-blue text-white rounded-full flex items-center justify-center shadow-2xl transition-all"
              >
                <Zap size={32} fill="currentColor" />
              </motion.div>
            </div>
            <img 
              src="https://picsum.photos/seed/tech/1200/800?blur=2" 
              alt="DX Video Placeholder" 
              className="w-full h-full object-cover opacity-40 group-hover:opacity-60 transition-opacity"
              referrerPolicy="no-referrer"
            />
            <div className="absolute bottom-8 left-8 right-8 flex justify-between items-center z-10">
              <div className="text-xs font-bold uppercase tracking-widest text-white/60">Interactive Infographic</div>
              <div className="text-xs font-bold uppercase tracking-widest text-white/60">03:45</div>
            </div>
          </motion.div>
        </div>

        <div className="flex justify-center mb-16">
          <div className="p-1 bg-white/5 rounded-full border border-white/10 flex">
            <button 
              onClick={() => setActiveTab('domains')}
              className={cn(
                "px-8 py-3 rounded-full text-sm font-bold transition-all",
                activeTab === 'domains' ? "bg-cyan-blue text-white shadow-lg" : "text-white/50 hover:text-white"
              )}
            >
              3 Core Domains
            </button>
            <button 
              onClick={() => setActiveTab('programs')}
              className={cn(
                "px-8 py-3 rounded-full text-sm font-bold transition-all",
                activeTab === 'programs' ? "bg-cyan-blue text-white shadow-lg" : "text-white/50 hover:text-white"
              )}
            >
              6 Sub-Programs
            </button>
          </div>
        </div>

        <AnimatePresence mode="wait">
          {activeTab === 'domains' ? (
            <motion.div 
              key="domains"
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -20 }}
              className="grid md:grid-cols-3 gap-8"
            >
              {domains.map((domain, idx) => (
                <motion.div 
                  key={domain.title}
                  whileHover={{ y: -10 }}
                  className="group relative p-10 bg-white/5 border border-white/10 rounded-[3rem] overflow-hidden"
                >
                  <div className={cn("absolute top-0 left-0 w-full h-1 opacity-0 group-hover:opacity-100 transition-opacity", domain.color)} />
                  <div className="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-cyan-blue transition-colors">
                    {domain.icon}
                  </div>
                  <h3 className="text-2xl font-bold mb-4">{domain.title}</h3>
                  <p className="text-white/50 leading-relaxed">{domain.desc}</p>
                  
                  <div className="mt-8 flex items-center gap-2 text-cyan-blue font-bold text-sm opacity-0 group-hover:opacity-100 transition-all translate-y-2 group-hover:translate-y-0">
                    Learn more <ArrowRight size={16} />
                  </div>
                </motion.div>
              ))}
            </motion.div>
          ) : (
            <motion.div 
              key="programs"
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -20 }}
              className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6"
            >
              {programs.map((prog, idx) => (
                <motion.div 
                  key={prog}
                  initial={{ opacity: 0, scale: 0.9 }}
                  animate={{ opacity: 1, scale: 1 }}
                  transition={{ delay: idx * 0.05 }}
                  className="p-8 bg-white/5 rounded-3xl border border-white/5 hover:border-cyan-blue/30 transition-all flex flex-col items-center text-center"
                >
                  <div className="w-12 h-12 bg-cyan-blue/20 text-cyan-blue rounded-full flex items-center justify-center mb-6 font-mono text-lg font-bold">
                    0{idx + 1}
                  </div>
                  <h4 className="text-xl font-bold">{prog}</h4>
                  <p className="mt-4 text-sm text-white/40">Strategic initiative driving digital excellence across the DOST ecosystem.</p>
                </motion.div>
              ))}
            </motion.div>
          )}
        </AnimatePresence>
      </div>

      {/* DX Roadmap Modal */}
      <AnimatePresence>
        {isRoadmapOpen && (
          <div className="fixed inset-0 z-[100] flex items-center justify-center p-6">
            <motion.div 
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              onClick={() => setIsRoadmapOpen(false)}
              className="absolute inset-0 bg-black/80 backdrop-blur-md"
            />
            <motion.div 
              initial={{ opacity: 0, scale: 0.9, y: 20 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.9, y: 20 }}
              className="relative w-full max-w-4xl bg-dark-card rounded-[3rem] shadow-2xl overflow-hidden border border-white/10"
            >
              <button 
                onClick={() => setIsRoadmapOpen(false)}
                className="absolute top-8 right-8 p-2 rounded-full bg-white/10 hover:bg-white hover:text-black transition-all"
              >
                <X size={20} />
              </button>
              
              <div className="p-12">
                <div className="flex items-center gap-4 mb-8">
                  <div className="w-12 h-12 bg-cyan-blue text-white rounded-2xl flex items-center justify-center">
                    <Calendar size={24} />
                  </div>
                  <h2 className="text-4xl font-bold">DOST DX Roadmap 2024-2028</h2>
                </div>

                <div className="space-y-12 relative before:absolute before:left-6 before:top-0 before:bottom-0 before:w-0.5 before:bg-white/10">
                  {[
                    { year: '2024', title: 'Foundation & Infrastructure', desc: 'Upgrading core network systems and cloud migration.' },
                    { year: '2025', title: 'Service Digitization', desc: 'Launching unified citizen portals and automated internal workflows.' },
                    { year: '2026', title: 'Data Intelligence', desc: 'Implementing AI-driven analytics for strategic decision making.' },
                    { year: '2027', title: 'Ecosystem Integration', desc: 'Seamless data sharing across all DOST agencies and partners.' },
                    { year: '2028', title: 'Digital Excellence', desc: 'Full organizational agility and global leadership in S&T digital services.' }
                  ].map((step, idx) => (
                    <motion.div 
                      key={step.year}
                      initial={{ opacity: 0, x: -20 }}
                      whileInView={{ opacity: 1, x: 0 }}
                      transition={{ delay: idx * 0.1 }}
                      className="relative pl-16"
                    >
                      <div className="absolute left-4 top-1 w-4 h-4 bg-cyan-blue rounded-full border-4 border-dark-card shadow-[0_0_0_4px_rgba(0,174,239,0.2)]" />
                      <div className="text-cyan-blue font-bold text-xl mb-1">{step.year}</div>
                      <h3 className="text-2xl font-bold mb-2">{step.title}</h3>
                      <p className="text-white/50 text-lg">{step.desc}</p>
                    </motion.div>
                  ))}
                </div>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </section>
  );
};

const AIAssistant = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [input, setInput] = useState('');
  const [messages, setMessages] = useState<{ role: string; text: string }[]>([
    { role: 'model', text: 'Hello! I am your PES AI Assistant. How can I help you today?' }
  ]);
  const [isLoading, setIsLoading] = useState(false);
  const scrollRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (scrollRef.current) {
      scrollRef.current.scrollTop = scrollRef.current.scrollHeight;
    }
  }, [messages]);

  const handleSend = async (textOverride?: string) => {
    const messageText = textOverride || input;
    if (!messageText.trim() || isLoading) return;

    const userMsg = { role: 'user', text: messageText };
    setMessages(prev => [...prev, userMsg]);
    setInput('');
    setIsLoading(true);

    const history = messages.map(m => ({
      role: m.role,
      parts: [{ text: m.text }]
    }));

    const response = await getGeminiResponse(messageText, history);
    setMessages(prev => [...prev, { role: 'model', text: response }]);
    setIsLoading(false);
  };

  const suggestions = [
    "What is the PES mandate?",
    "Show me latest issuances",
    "Tell me about DOST DX",
    "Who is Dir. Pedraza?"
  ];

  const quickActions = [
    { label: "Issuances", icon: <FileText size={14} />, query: "Show me the latest issuances" },
    { label: "Mandate", icon: <Info size={14} />, query: "What is the PES mandate?" },
    { label: "Divisions", icon: <Layers size={14} />, query: "What are the PES divisions?" },
    { label: "DX", icon: <Zap size={14} />, query: "Tell me about DOST DX" },
  ];

  return (
    <div className="fixed bottom-8 right-8 z-[100]">
      <AnimatePresence>
        {isOpen && (
          <motion.div 
            initial={{ opacity: 0, scale: 0.8, y: 20 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.8, y: 20 }}
            className="absolute bottom-20 right-0 w-[380px] h-[600px] bg-white dark:bg-dark-card rounded-[2.5rem] shadow-2xl border border-black/5 dark:border-white/5 flex flex-col overflow-hidden"
          >
            <div className="p-6 bg-black dark:bg-cyan-blue text-white flex justify-between items-center">
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 bg-cyan-blue dark:bg-white dark:text-cyan-blue rounded-lg flex items-center justify-center">
                  <Zap size={16} />
                </div>
                <span className="font-bold">PES Assistant</span>
              </div>
              <button onClick={() => setIsOpen(false)}><X size={20} /></button>
            </div>

            <div className="px-6 py-4 border-b border-black/5 dark:border-white/5 flex gap-2 overflow-x-auto no-scrollbar">
              {quickActions.map(action => (
                <button 
                  key={action.label}
                  onClick={() => handleSend(action.query)}
                  className="flex items-center gap-2 px-3 py-1.5 bg-apple-gray dark:bg-white/10 rounded-full text-xs font-bold whitespace-nowrap hover:bg-cyan-blue hover:text-white transition-all"
                >
                  {action.icon} {action.label}
                </button>
              ))}
            </div>

            <div ref={scrollRef} className="flex-1 overflow-y-auto p-6 space-y-4">
              {messages.map((m, i) => (
                <div key={i} className={cn(
                  "max-w-[85%] p-4 rounded-2xl text-sm leading-relaxed",
                  m.role === 'user' ? "bg-cyan-blue text-white ml-auto rounded-tr-none" : "bg-apple-gray dark:bg-white/10 text-black dark:text-white mr-auto rounded-tl-none"
                )}>
                  <Typewriter text={m.text} delay={10} active={i === messages.length - 1 && m.role === 'model'} />
                </div>
              ))}
              {isLoading && (
                <div className="bg-apple-gray dark:bg-white/10 text-black dark:text-white mr-auto rounded-2xl rounded-tl-none p-4 text-sm animate-pulse flex gap-2 items-center">
                  <div className="w-1.5 h-1.5 bg-cyan-blue rounded-full animate-bounce" />
                  <div className="w-1.5 h-1.5 bg-cyan-blue rounded-full animate-bounce [animation-delay:0.2s]" />
                  <div className="w-1.5 h-1.5 bg-cyan-blue rounded-full animate-bounce [animation-delay:0.4s]" />
                </div>
              )}
              
              {!isLoading && messages.length === 1 && (
                <div className="grid grid-cols-1 gap-2 pt-4">
                  <p className="text-[10px] font-bold text-black/30 dark:text-white/30 uppercase tracking-widest mb-1">Suggestions</p>
                  {suggestions.map(s => (
                    <button 
                      key={s}
                      onClick={() => handleSend(s)}
                      className="text-left px-4 py-3 bg-apple-gray dark:bg-white/10 hover:bg-black hover:text-white dark:hover:bg-cyan-blue rounded-xl text-xs transition-all flex items-center justify-between group"
                    >
                      {s} <ArrowRight size={14} className="opacity-0 group-hover:opacity-100 transition-opacity" />
                    </button>
                  ))}
                </div>
              )}
            </div>

            <div className="p-4 border-t border-black/5 dark:border-white/5 flex gap-2">
              <input 
                type="text" 
                placeholder="Ask me anything..."
                className="flex-1 bg-apple-gray dark:bg-white/10 rounded-full px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-blue/20 dark:text-white"
                value={input}
                onChange={(e) => setInput(e.target.value)}
                onKeyDown={(e) => e.key === 'Enter' && handleSend()}
              />
              <button 
                onClick={handleSend}
                className="w-12 h-12 bg-black dark:bg-cyan-blue text-white rounded-full flex items-center justify-center hover:bg-black/80 transition-all shadow-lg active:scale-95"
              >
                <ArrowRight size={18} />
              </button>
            </div>
          </motion.div>
        )}
      </AnimatePresence>

      <motion.button 
        whileHover={{ scale: 1.1 }}
        whileTap={{ scale: 0.9 }}
        onClick={() => setIsOpen(!isOpen)}
        className="w-16 h-16 bg-cyan-blue text-white rounded-full shadow-2xl flex items-center justify-center relative group"
      >
        <div className="absolute inset-0 bg-cyan-blue rounded-full animate-ping opacity-20 group-hover:opacity-0 transition-opacity" />
        {isOpen ? <X size={28} /> : <MessageSquare size={28} />}
      </motion.button>
    </div>
  );
};

const Typewriter = ({ text, delay, active }: { text: string, delay: number, active: boolean }) => {
  const [currentText, setCurrentText] = useState(active ? '' : text);
  const [currentIndex, setCurrentIndex] = useState(0);

  useEffect(() => {
    if (!active) return;
    if (currentIndex < text.length) {
      const timeout = setTimeout(() => {
        setCurrentText(prevText => prevText + text[currentIndex]);
        setCurrentIndex(prevIndex => prevIndex + 1);
      }, delay);
      return () => clearTimeout(timeout);
    }
  }, [currentIndex, delay, text, active]);

  return <span>{currentText}</span>;
};

const Footer = () => {
  return (
    <footer className="section-padding bg-apple-gray dark:bg-dark-card border-t border-black/5 dark:border-white/5 transition-colors">
      <div className="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
        <div>
          <div className="text-xl font-bold mb-2 dark:text-white">PES Portal</div>
          <p className="text-black/40 dark:text-white/40 text-sm">© 2024 DOST Planning and Evaluation Service. All rights reserved.</p>
        </div>
        <div className="flex gap-8">
          <a href="#" className="text-sm text-black/60 dark:text-white/60 hover:text-cyan-blue transition-colors">Privacy Policy</a>
          <a href="#" className="text-sm text-black/60 dark:text-white/60 hover:text-cyan-blue transition-colors">Terms of Service</a>
          <a href="#" className="text-sm text-black/60 dark:text-white/60 hover:text-cyan-blue transition-colors">Contact Us</a>
        </div>
      </div>
    </footer>
  );
};

// --- Pages ---

const AnalyticsSection = () => {
  const data = [
    { name: 'Jan', issuances: 4, materials: 2 },
    { name: 'Feb', issuances: 7, materials: 3 },
    { name: 'Mar', issuances: 5, materials: 8 },
    { name: 'Apr', issuances: 12, materials: 4 },
    { name: 'May', issuances: 9, materials: 6 },
    { name: 'Jun', issuances: 15, materials: 10 },
  ];

  const COLORS = ['#00AEEF', '#000000'];

  return (
    <section className="section-padding bg-apple-gray dark:bg-dark-bg/50">
      <div className="max-w-7xl mx-auto">
        <div className="grid lg:grid-cols-3 gap-12 items-center">
          <div className="lg:col-span-1">
            <Reveal>
              <div className="inline-flex items-center gap-2 px-4 py-2 bg-cyan-blue/10 text-cyan-blue rounded-full text-xs font-bold uppercase tracking-widest mb-6">
                <TrendingUp size={14} /> PES at a Glance
              </div>
            </Reveal>
            <Reveal>
              <h2 className="text-4xl font-bold tracking-tight mb-6">Data-Driven Transparency.</h2>
            </Reveal>
            <Reveal>
              <p className="text-lg text-black/60 dark:text-white/60 mb-8">
                We track our productivity and impact through real-time analytics. See how PES is actively contributing to DOST's mission.
              </p>
            </Reveal>
            <div className="space-y-4">
              <motion.div 
                whileHover={{ x: 10 }}
                className="flex items-center gap-4 p-4 bg-white dark:bg-dark-card rounded-2xl shadow-sm border border-black/5 dark:border-white/5"
              >
                <div className="w-12 h-12 bg-cyan-blue text-white rounded-xl flex items-center justify-center">
                  <FileText size={24} />
                </div>
                <div>
                  <div className="text-2xl font-bold">124</div>
                  <div className="text-xs text-black/40 dark:text-white/40 uppercase font-bold">Total Issuances 2024</div>
                </div>
              </motion.div>
              <motion.div 
                whileHover={{ x: 10 }}
                className="flex items-center gap-4 p-4 bg-white dark:bg-dark-card rounded-2xl shadow-sm border border-black/5 dark:border-white/5"
              >
                <div className="w-12 h-12 bg-black dark:bg-white dark:text-black text-white rounded-xl flex items-center justify-center">
                  <Download size={24} />
                </div>
                <div>
                  <div className="text-2xl font-bold">850+</div>
                  <div className="text-xs text-black/40 dark:text-white/40 uppercase font-bold">Material Downloads</div>
                </div>
              </motion.div>
            </div>
          </div>

          <div className="lg:col-span-2 bg-white dark:bg-dark-card p-8 rounded-[3rem] shadow-xl border border-black/5 dark:border-white/5 h-[400px]">
            <h3 className="text-xl font-bold mb-8 flex items-center gap-2">
              <BarChart3 size={20} className="text-cyan-blue" /> Monthly Activity Trend
            </h3>
            <ResponsiveContainer width="100%" height="80%">
              <BarChart data={data}>
                <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#00000010" />
                <XAxis 
                  dataKey="name" 
                  axisLine={false} 
                  tickLine={false} 
                  tick={{ fontSize: 12, fill: '#888' }} 
                />
                <YAxis 
                  axisLine={false} 
                  tickLine={false} 
                  tick={{ fontSize: 12, fill: '#888' }} 
                />
                <Tooltip 
                  cursor={{ fill: '#00AEEF10' }}
                  contentStyle={{ 
                    borderRadius: '16px', 
                    border: 'none', 
                    boxShadow: '0 10px 30px rgba(0,0,0,0.1)',
                    padding: '12px'
                  }}
                />
                <Bar dataKey="issuances" fill="#00AEEF" radius={[4, 4, 0, 0]} barSize={30} />
                <Bar dataKey="materials" fill="#000000" radius={[4, 4, 0, 0]} barSize={30} />
              </BarChart>
            </ResponsiveContainer>
            <div className="flex justify-center gap-8 mt-4">
              <div className="flex items-center gap-2 text-xs font-bold">
                <div className="w-3 h-3 bg-cyan-blue rounded-full" /> Issuances
              </div>
              <div className="flex items-center gap-2 text-xs font-bold">
                <div className="w-3 h-3 bg-black dark:bg-white rounded-full" /> Materials
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

const LandingPage = () => {
  const { scrollYProgress } = useScroll();
  const scaleX = useSpring(scrollYProgress, {
    stiffness: 100,
    damping: 30,
    restDelta: 0.001
  });

  return (
    <div className="scroll-smooth">
      <CustomCursor />
      <motion.div className="scroll-progress" style={{ scaleX }} />
      <Navbar />
      <Hero />
      <WhatsNewSection />
      <MandateSection />
      <AnalyticsSection />
      <DivisionsSection />
      <IssuancesSection />
      <MaterialsSection />
      <DOSTDXSection />
      <ContactSection />
      <SubscriptionSection />
      <AIAssistant />
      <Footer />
    </div>
  );
};

const AdminDashboard = () => {
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [password, setPassword] = useState('');
  const [activeTab, setActiveTab] = useState<'issuances' | 'divisions' | 'dx' | 'categories'>('issuances');
  
  const [issuances, setIssuances] = useState<Issuance[]>([]);
  const [divisions, setDivisions] = useState<Division[]>([]);
  const [dxContent, setDXContent] = useState<DXContent[]>([]);
  const [categories, setCategories] = useState<Category[]>([]);

  const [newIssuance, setNewIssuance] = useState({ title: '', category: '', date: '', division: '', url: '#' });
  const [newDivision, setNewDivision] = useState({ name: '', description: '', head: '' });
  const [newDX, setNewDX] = useState({ category: 'domain', title: '', description: '' });
  const [newCategory, setNewCategory] = useState('');

  useEffect(() => {
    if (isLoggedIn) {
      fetch('/api/issuances').then(res => res.json()).then(setIssuances);
      fetch('/api/divisions').then(res => res.json()).then(setDivisions);
      fetch('/api/dost-dx').then(res => res.json()).then(setDXContent);
      fetch('/api/categories').then(res => res.json()).then(data => {
        setCategories(data);
        if (data.length > 0) setNewIssuance(prev => ({ ...prev, category: data[0].name }));
      });
    }
  }, [isLoggedIn]);

  const handleLogin = async () => {
    const res = await fetch('/api/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ password })
    });
    if (res.ok) setIsLoggedIn(true);
    else alert('Invalid password');
  };

  const handleAddIssuance = async () => {
    const res = await fetch('/api/issuances', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(newIssuance)
    });
    if (res.ok) {
      const data = await res.json();
      setIssuances([{ ...newIssuance, id: data.id }, ...issuances]);
      setNewIssuance({ title: '', category: 'Memorandum', date: '', division: '', url: '#' });
    }
  };

  const handleAddDivision = async () => {
    const res = await fetch('/api/divisions', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(newDivision)
    });
    if (res.ok) {
      const data = await res.json();
      setDivisions([...divisions, { ...newDivision, id: data.id }]);
      setNewDivision({ name: '', description: '', head: '' });
    }
  };

  const handleAddDX = async () => {
    const res = await fetch('/api/dost-dx', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(newDX)
    });
    if (res.ok) {
      const data = await res.json();
      setDXContent([...dxContent, { ...newDX, id: data.id }]);
      setNewDX({ category: 'domain', title: '', description: '' });
    }
  };

  const handleAddCategory = async () => {
    if (!newCategory) return;
    const res = await fetch('/api/categories', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name: newCategory })
    });
    if (res.ok) {
      const data = await res.json();
      setCategories([...categories, { id: data.id, name: newCategory }]);
      setNewCategory('');
    }
  };

  const handleDelete = async (type: string, id: number) => {
    const res = await fetch(`/api/${type}/${id}`, { method: 'DELETE' });
    if (res.ok) {
      if (type === 'issuances') setIssuances(issuances.filter(i => i.id !== id));
      if (type === 'divisions') setDivisions(divisions.filter(i => i.id !== id));
      if (type === 'dost-dx') setDXContent(dxContent.filter(i => i.id !== id));
      if (type === 'categories') setCategories(categories.filter(c => c.id !== id));
    }
  };

  if (!isLoggedIn) {
    return (
      <div className="h-screen flex items-center justify-center bg-apple-gray">
        <div className="bg-white p-10 rounded-[2.5rem] shadow-2xl border border-black/5 w-full max-w-md">
          <div className="text-center mb-8">
            <div className="w-16 h-16 bg-cyan-blue rounded-2xl flex items-center justify-center text-white mx-auto mb-4">
              <Shield size={32} />
            </div>
            <h1 className="text-2xl font-bold">Admin Access</h1>
            <p className="text-black/40">Please enter the administrative password.</p>
          </div>
          <input 
            type="password" 
            className="w-full px-6 py-4 bg-apple-gray rounded-2xl border-none focus:ring-2 focus:ring-cyan-blue/20 mb-4"
            placeholder="Password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            onKeyDown={(e) => e.key === 'Enter' && handleLogin()}
          />
          <button 
            onClick={handleLogin}
            className="w-full bg-black text-white py-4 rounded-2xl font-bold hover:bg-black/80 transition-all"
          >
            Login
          </button>
          <Link to="/" className="block text-center mt-6 text-sm text-black/40 hover:text-black transition-colors">
            Back to Public Portal
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-apple-gray flex">
      {/* Sidebar */}
      <aside className="w-72 bg-white border-r border-black/5 p-8 flex flex-col">
        <div className="text-xl font-bold mb-12 flex items-center gap-2">
          <LayoutDashboard className="text-cyan-blue" />
          <span>PES Admin</span>
        </div>
        
        <nav className="flex-1 space-y-2">
          <button 
            onClick={() => setActiveTab('issuances')}
            className={cn(
              "w-full text-left px-4 py-3 rounded-xl font-medium flex items-center gap-3 transition-all",
              activeTab === 'issuances' ? "bg-cyan-blue/10 text-cyan-blue" : "text-black/50 hover:bg-black/5"
            )}
          >
            <FileText size={18} /> Issuances
          </button>
          <button 
            onClick={() => setActiveTab('divisions')}
            className={cn(
              "w-full text-left px-4 py-3 rounded-xl font-medium flex items-center gap-3 transition-all",
              activeTab === 'divisions' ? "bg-cyan-blue/10 text-cyan-blue" : "text-black/50 hover:bg-black/5"
            )}
          >
            <Layers size={18} /> Divisions
          </button>
          <button 
            onClick={() => setActiveTab('dx')}
            className={cn(
              "w-full text-left px-4 py-3 rounded-xl font-medium flex items-center gap-3 transition-all",
              activeTab === 'dx' ? "bg-cyan-blue/10 text-cyan-blue" : "text-black/50 hover:bg-black/5"
            )}
          >
            <Zap size={18} /> DOST DX
          </button>
          <button 
            onClick={() => setActiveTab('categories')}
            className={cn(
              "w-full text-left px-4 py-3 rounded-xl font-medium flex items-center gap-3 transition-all",
              activeTab === 'categories' ? "bg-cyan-blue/10 text-cyan-blue" : "text-black/50 hover:bg-black/5"
            )}
          >
            <Filter size={18} /> Categories
          </button>
        </nav>

        <button 
          onClick={() => setIsLoggedIn(false)}
          className="mt-auto flex items-center gap-2 text-red-500 font-medium hover:underline"
        >
          <LogOut size={18} /> Logout
        </button>
      </aside>

      {/* Main Content */}
      <main className="flex-1 p-12 overflow-y-auto">
        <header className="flex justify-between items-center mb-12">
          <div>
            <h1 className="text-3xl font-bold tracking-tight">
              {activeTab === 'issuances' && 'Manage Issuances'}
              {activeTab === 'divisions' && 'Manage Divisions'}
              {activeTab === 'dx' && 'Manage DOST DX'}
              {activeTab === 'categories' && 'Manage Categories'}
            </h1>
            <p className="text-black/40">Administrative control panel for PES portal.</p>
          </div>
        </header>

        {/* Issuances Tab */}
        {activeTab === 'issuances' && (
          <>
            <div className="bg-white p-8 rounded-[2rem] shadow-sm border border-black/5 mb-12">
              <h2 className="text-xl font-bold mb-6 flex items-center gap-2">
                <Plus size={20} className="text-cyan-blue" /> Add New Issuance
              </h2>
              <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                <input 
                  type="text" 
                  placeholder="Title"
                  className="px-4 py-3 bg-apple-gray rounded-xl border-none focus:ring-2 focus:ring-cyan-blue/20"
                  value={newIssuance.title}
                  onChange={(e) => setNewIssuance({...newIssuance, title: e.target.value})}
                />
                <select 
                  className="px-4 py-3 bg-apple-gray rounded-xl border-none focus:ring-2 focus:ring-cyan-blue/20"
                  value={newIssuance.category}
                  onChange={(e) => setNewIssuance({...newIssuance, category: e.target.value})}
                >
                  {categories.map(cat => (
                    <option key={cat.id} value={cat.name}>{cat.name}</option>
                  ))}
                </select>
                <input 
                  type="date" 
                  className="px-4 py-3 bg-apple-gray rounded-xl border-none focus:ring-2 focus:ring-cyan-blue/20"
                  value={newIssuance.date}
                  onChange={(e) => setNewIssuance({...newIssuance, date: e.target.value})}
                />
                <input 
                  type="text" 
                  placeholder="Division"
                  className="px-4 py-3 bg-apple-gray rounded-xl border-none focus:ring-2 focus:ring-cyan-blue/20"
                  value={newIssuance.division}
                  onChange={(e) => setNewIssuance({...newIssuance, division: e.target.value})}
                />
              </div>
              <button 
                onClick={handleAddIssuance}
                className="mt-6 bg-cyan-blue text-white px-8 py-3 rounded-xl font-bold hover:bg-cyan-blue/90 transition-all"
              >
                Publish Issuance
              </button>
            </div>

            <div className="bg-white rounded-[2rem] shadow-sm border border-black/5 overflow-hidden">
              <table className="w-full text-left">
                <thead>
                  <tr className="bg-apple-gray/50 border-b border-black/5">
                    <th className="px-8 py-4 text-sm font-semibold">Title</th>
                    <th className="px-8 py-4 text-sm font-semibold">Category</th>
                    <th className="px-8 py-4 text-sm font-semibold text-right">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {issuances.map(item => (
                    <tr key={item.id} className="border-b border-black/5 last:border-0">
                      <td className="px-8 py-4 font-medium">{item.title}</td>
                      <td className="px-8 py-4 text-black/50">{item.category}</td>
                      <td className="px-8 py-4 text-right">
                        <button onClick={() => handleDelete('issuances', item.id)} className="text-red-500 p-2"><Trash2 size={18} /></button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </>
        )}

        {/* Divisions Tab */}
        {activeTab === 'divisions' && (
          <>
            <div className="bg-white p-8 rounded-[2rem] shadow-sm border border-black/5 mb-12">
              <h2 className="text-xl font-bold mb-6 flex items-center gap-2">
                <Plus size={20} className="text-cyan-blue" /> Add New Division
              </h2>
              <div className="grid md:grid-cols-2 gap-4">
                <input 
                  type="text" 
                  placeholder="Division Name"
                  className="px-4 py-3 bg-apple-gray rounded-xl border-none focus:ring-2 focus:ring-cyan-blue/20"
                  value={newDivision.name}
                  onChange={(e) => setNewDivision({...newDivision, name: e.target.value})}
                />
                <input 
                  type="text" 
                  placeholder="Head of Division"
                  className="px-4 py-3 bg-apple-gray rounded-xl border-none focus:ring-2 focus:ring-cyan-blue/20"
                  value={newDivision.head}
                  onChange={(e) => setNewDivision({...newDivision, head: e.target.value})}
                />
                <textarea 
                  placeholder="Description"
                  className="md:col-span-2 px-4 py-3 bg-apple-gray rounded-xl border-none focus:ring-2 focus:ring-cyan-blue/20 min-h-[100px]"
                  value={newDivision.description}
                  onChange={(e) => setNewDivision({...newDivision, description: e.target.value})}
                />
              </div>
              <button 
                onClick={handleAddDivision}
                className="mt-6 bg-cyan-blue text-white px-8 py-3 rounded-xl font-bold hover:bg-cyan-blue/90 transition-all"
              >
                Save Division
              </button>
            </div>

            <div className="bg-white rounded-[2rem] shadow-sm border border-black/5 overflow-hidden">
              <table className="w-full text-left">
                <thead>
                  <tr className="bg-apple-gray/50 border-b border-black/5">
                    <th className="px-8 py-4 text-sm font-semibold">Name</th>
                    <th className="px-8 py-4 text-sm font-semibold">Head</th>
                    <th className="px-8 py-4 text-sm font-semibold text-right">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {divisions.map(item => (
                    <tr key={item.id} className="border-b border-black/5 last:border-0">
                      <td className="px-8 py-4 font-medium">{item.name}</td>
                      <td className="px-8 py-4 text-black/50">{item.head}</td>
                      <td className="px-8 py-4 text-right">
                        <button onClick={() => handleDelete('divisions', item.id)} className="text-red-500 p-2"><Trash2 size={18} /></button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </>
        )}

        {/* DOST DX Tab */}
        {activeTab === 'dx' && (
          <>
            <div className="bg-white p-8 rounded-[2rem] shadow-sm border border-black/5 mb-12">
              <h2 className="text-xl font-bold mb-6 flex items-center gap-2">
                <Plus size={20} className="text-cyan-blue" /> Add DX Content
              </h2>
              <div className="grid md:grid-cols-2 gap-4">
                <select 
                  className="px-4 py-3 bg-apple-gray rounded-xl border-none focus:ring-2 focus:ring-cyan-blue/20"
                  value={newDX.category}
                  onChange={(e) => setNewDX({...newDX, category: e.target.value})}
                >
                  <option value="domain">Domain</option>
                  <option value="program">Sub-Program</option>
                </select>
                <input 
                  type="text" 
                  placeholder="Title"
                  className="px-4 py-3 bg-apple-gray rounded-xl border-none focus:ring-2 focus:ring-cyan-blue/20"
                  value={newDX.title}
                  onChange={(e) => setNewDX({...newDX, title: e.target.value})}
                />
                <textarea 
                  placeholder="Description"
                  className="md:col-span-2 px-4 py-3 bg-apple-gray rounded-xl border-none focus:ring-2 focus:ring-cyan-blue/20 min-h-[100px]"
                  value={newDX.description}
                  onChange={(e) => setNewDX({...newDX, description: e.target.value})}
                />
              </div>
              <button 
                onClick={handleAddDX}
                className="mt-6 bg-cyan-blue text-white px-8 py-3 rounded-xl font-bold hover:bg-cyan-blue/90 transition-all"
              >
                Save DX Content
              </button>
            </div>

            <div className="bg-white rounded-[2rem] shadow-sm border border-black/5 overflow-hidden">
              <table className="w-full text-left">
                <thead>
                  <tr className="bg-apple-gray/50 border-b border-black/5">
                    <th className="px-8 py-4 text-sm font-semibold">Title</th>
                    <th className="px-8 py-4 text-sm font-semibold">Category</th>
                    <th className="px-8 py-4 text-sm font-semibold text-right">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {dxContent.map(item => (
                    <tr key={item.id} className="border-b border-black/5 last:border-0">
                      <td className="px-8 py-4 font-medium">{item.title}</td>
                      <td className="px-8 py-4 text-black/50 uppercase text-xs font-bold">{item.category}</td>
                      <td className="px-8 py-4 text-right">
                        <button onClick={() => handleDelete('dost-dx', item.id)} className="text-red-500 p-2"><Trash2 size={18} /></button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </>
        )}
        {/* Categories Tab */}
        {activeTab === 'categories' && (
          <>
            <div className="bg-white p-8 rounded-[2rem] shadow-sm border border-black/5 mb-12">
              <h2 className="text-xl font-bold mb-6 flex items-center gap-2">
                <Plus size={20} className="text-cyan-blue" /> Add New Category
              </h2>
              <div className="flex gap-4">
                <input 
                  type="text" 
                  placeholder="Category Name"
                  className="flex-1 px-4 py-3 bg-apple-gray rounded-xl border-none focus:ring-2 focus:ring-cyan-blue/20"
                  value={newCategory}
                  onChange={(e) => setNewCategory(e.target.value)}
                />
                <button 
                  onClick={handleAddCategory}
                  className="bg-cyan-blue text-white px-8 py-3 rounded-xl font-bold hover:bg-cyan-blue/90 transition-all"
                >
                  Add Category
                </button>
              </div>
            </div>

            <div className="bg-white rounded-[2rem] shadow-sm border border-black/5 overflow-hidden">
              <table className="w-full text-left">
                <thead>
                  <tr className="bg-apple-gray/50 border-b border-black/5">
                    <th className="px-8 py-4 text-sm font-semibold">Category Name</th>
                    <th className="px-8 py-4 text-sm font-semibold text-right">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {categories.map(item => (
                    <tr key={item.id} className="border-b border-black/5 last:border-0">
                      <td className="px-8 py-4 font-medium">{item.name}</td>
                      <td className="px-8 py-4 text-right">
                        <button onClick={() => handleDelete('categories', item.id)} className="text-red-500 p-2"><Trash2 size={18} /></button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </>
        )}
      </main>
    </div>
  );
};

// --- Main App ---

export default function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<LandingPage />} />
        <Route path="/admin" element={<AdminDashboard />} />
      </Routes>
    </Router>
  );
}
