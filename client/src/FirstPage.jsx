import React from "react";
import bgImage from "./assets/bg.jpg";
import salad from "./assets/salad.jpg";
import apples from "./assets/apples.png";

const FirstPage = () => {
  return (
    <>
      <div
        className="w-screen min-h-screen bg-center bg-contain"
        style={{ backgroundImage: `url(${bgImage})` }}
      >
        <div className="absolute inset-0 bg-[#C37B69] opacity-70"></div>
        <div className="relative z-10 flex flex-col items-center justify-center h-full">
          <h1 className="mt-10 text-xl font-bold text-black sm:text-3xl md:text-4xl ">
            Your guide to junk-free living.
          </h1>
          <div className="w-3/4 m-4 bg-black h-0.5"></div>
        </div>

        <div className="relative z-10 flex flex-wrap justify-end gap-4 px-4 mt-6">
          <button className="px-3 py-2 text-xs sm:px-6 sm:py-3 sm:text-sm text-white bg-[#385733] rounded-full hover:bg-[#5A8840]">
            Login
          </button>
          <button className="px-3 py-2 text-xs sm:px-6 sm:py-3 sm:text-sm text-white bg-[#385733] rounded-full hover:bg-[#5A8840]">
            Report
          </button>
          <button className="px-3 py-2 text-xs sm:px-6 sm:py-3 sm:text-sm text-white bg-[#385733] rounded-full hover:bg-[#5A8840]">
            Level
          </button>
        </div>
        <div className="relative z-10 flex justify-center w-full mt-10 sm:justify-start sm:pl-32">
          <div className="relative w-72 sm:w-96 bg-[#385733] opacity-80 rounded-xl p-6 sm:p-10 text-white border-2 border-black">
            <p className="text-sm leading-relaxed sm:text-base">
              Join the challenge, track your progress, and see how cutting down
              on junk food changes your energy and mood.
            </p>
            <h2 className="mt-6 ml-24 text-lg font-bold sm:text-2xl sm:ml-32">
              Let's <br />
              make <br />
              life <br />
              healthier <br />
              with us.
            </h2>

            {/* Salad Image */}
            <img
              src={salad}
              alt="Salad"
              className="absolute object-cover rounded-full shadow-xl w-28 h-28 -left-10 bottom-8 sm:w-40 sm:h-40 sm:-left-20"
            />

            <img
              src={apples}
              alt="Apples"
              className="absolute object-contain w-16 h-16 right-1 bottom-2 sm:w-20 sm:h-20"
            />
          </div>
        </div>
        <div className="flex justify-center mt-24 mb-10 sm:-mt-16 sm:justify-end sm:pr-32">
          <button className="z-10 px-6 py-3 text-sm sm:text-base text-white bg-[#A44D4E] rounded-xl border-2 border-black shadow-md hover:scale-105 transition">
            Start Your Healthy Journey →
          </button>
        </div>
      </div>
    </>
  );
};

export default FirstPage;