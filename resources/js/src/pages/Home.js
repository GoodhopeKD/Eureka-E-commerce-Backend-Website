import React from "react";
import About from "../components/About";
import Contact from "../components/Contact";
import Features from "../components/Features";
import Nav from "../components/Nav";
import Overview from "../components/Overview";

const Home = () => {
  return (
    <>
      <Nav />
      <Overview />
      <Features />
      <About />
      <Contact />
    </>
  );
};

export default Home;
